<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use ErrorException;
use EventCandy\Sets\Core\Checkout\Cart\CartProduct\CartProductService;
use EventCandy\Sets\Core\Checkout\Cart\LineItemPriceService;
use EventCandy\Sets\Core\Checkout\Cart\Payload\PayloadService;
use EventCandy\Sets\Core\Content\DynamicProduct\Cart\DynamicProductGateway;
use EventCandy\Sets\Core\Content\DynamicProduct\Cart\DynamicProductService;
use EventCandy\Sets\Core\Content\DynamicProduct\DynamicProductEntity;
use EventCandy\Sets\Utils;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartPersisterInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class LabelMeCartCollector implements CartDataCollectorInterface
{
    public const TYPE = 'event-candy-label-me';

    /**
     * @var CartPersisterInterface
     */
    protected $cartPersister;

    /**
     * @var DynamicProductService
     */
    protected $dynamicProductService;
    /**
     * @var CartProductService
     */
    protected $cartProductService;

    /**
     * @var DynamicProductGateway
     */
    protected $dynamicProductGateway;

    /**
     * @var LineItemPriceService
     */
    protected $lineItemPriceService;

    /**
     * @var PayloadService
     */
    protected $payloadService;

    /**
     * @var EntityRepositoryInterface
     */
    protected $mediaRepository;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param CartPersisterInterface $cartPersister
     * @param DynamicProductService $dynamicProductService
     * @param CartProductService $cartProductService
     * @param DynamicProductGateway $dynamicProductGateway
     * @param LineItemPriceService $lineItemPriceService
     * @param PayloadService $payloadService
     * @param EntityRepositoryInterface $mediaRepository
     * @param Connection $connection
     */
    public function __construct(
        CartPersisterInterface $cartPersister,
        DynamicProductService $dynamicProductService,
        CartProductService $cartProductService,
        DynamicProductGateway $dynamicProductGateway,
        LineItemPriceService $lineItemPriceService,
        PayloadService $payloadService,
        EntityRepositoryInterface $mediaRepository,
        Connection $connection
    ) {
        $this->cartPersister = $cartPersister;
        $this->dynamicProductService = $dynamicProductService;
        $this->cartProductService = $cartProductService;
        $this->dynamicProductGateway = $dynamicProductGateway;
        $this->lineItemPriceService = $lineItemPriceService;
        $this->payloadService = $payloadService;
        $this->mediaRepository = $mediaRepository;
        $this->connection = $connection;
    }


    public function collect(
        CartDataCollection $data,
        Cart $original,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        $lineItemsChanged = $this->getNotCompleted($data, $original->getLineItems()->getElements(), $original->isModified());
        if (count($lineItemsChanged) === 0) {
            return;
        }

        //Utils::log('collectLM');

        $lineItems = $original->getLineItems()->filterFlatByType(self::TYPE);


        $this->createCartIfNotExists($context, $original);

        foreach ($lineItems as $lineItem) {
            // DB
            $this->dynamicProductService->removeDynamicProductsByLineItemId($lineItem->getId(), $context->getToken());
        }
        //DB
        $this->cartProductService->removeCartProductsByTokenAndType($context->getToken(), self::TYPE);
        $data->clear();

        $dynamicProducts = $this->dynamicProductService->createDynamicProductCollection($lineItems, $original->getToken());
        $this->dynamicProductService->saveDynamicProductsToDb($dynamicProducts);

        $dynamicProductIds = $this->dynamicProductService->getDynamicProductIdsFromCollection($dynamicProducts);
        $dynamicProductCollection = $this->dynamicProductGateway->get($dynamicProductIds, $context, false);
        $this->dynamicProductService->addDynamicProductsToCartDataByLineItemId($dynamicProductCollection, $data);


        $cartProducts = [];
        foreach ($lineItems as $lineItem) {
            $this->payloadService->loadPayloadDataForLineItem($lineItem, $data, $context);
            $cartProducts = array_merge($cartProducts, $this->cartProductService->buildCartProductsFromPayload($lineItem, $data, self::TYPE));
            $this->dynamicProductService->removeDynamicProductsFromCartDataByLineItemId($lineItem->getId(), $data);
        }
        $this->cartProductService->saveCartProducts($cartProducts);

        // repeat it again but with correct stock
        $dynamicProductCollection = $this->dynamicProductGateway->get($dynamicProductIds, $context);
        $this->dynamicProductService->addDynamicProductsToCartDataByLineItemId($dynamicProductCollection, $data);

        foreach ($lineItems as $lineItem) {
            $this->enrichLineItem($lineItem, $data, $context);
            $payloadItem = $this->payloadService->buildPayloadObject($lineItem, $data);
            $payloadAssociative = $this->payloadService->makePayloadDataAssociative($payloadItem, self::TYPE);
            $lineItem->setPayload($payloadAssociative);
        }

    }

    private function enrichLineItem(LineItem $lineItem, CartDataCollection $data, SalesChannelContext $context)
    {
        /** @var DynamicProductEntity $product */
        $dynamicProduct = $this->dynamicProductService->getFromCartDataByLineItemId($lineItem->getId(), $data)[0];
        $product = $dynamicProduct->getProduct();

        $payload = $lineItem->getPayload();
        $lineItem->setLabel($this->getProductName($payload));

        $cover = $this->getLineItemCover($payload, $context);
        $lineItem->setCover($cover);

        $deliveryTime = null;
        if ($product->getDeliveryTime() !== null) {
            $deliveryTime = DeliveryTime::createFromEntity($product->getDeliveryTime());
        }

        //$availableStock = $this->calculateAvailableStock($lineItem, $context);

        $lineItem->setDeliveryInformation(
            new DeliveryInformation(
                $product->getAvailableStock(),
                (float)$product->getWeight(),
                $product->getShippingFree(),
                $product->getRestockTime(),
                $deliveryTime,
                $product->getHeight(),
                $product->getWidth(),
                $product->getLength()
            )
        );


        if ($lineItem->getPriceDefinition() == null) {
            $qtyDefinition = $this->lineItemPriceService->buildQuantityPriceDefinition($lineItem, $data, $context);
            $lineItem->setPriceDefinition($qtyDefinition);
        }

        $quantityInformation = (new QuantityInformation())
            ->setMinPurchase($product->getMinPurchase() ?? 1)
            ->setMaxPurchase($product->getAvailableStock())
            ->setPurchaseSteps($product->getPurchaseSteps() ?? 1);
        $lineItem->setQuantityInformation($quantityInformation);

        $payload = [
            'isCloseout' => $product->getIsCloseout(),
            'customFields' => $product->getCustomFields(),
            'createdAt' => $product->getCreatedAt()->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'releaseDate' => $product->getReleaseDate() ? $product->getReleaseDate()->format(
                Defaults::STORAGE_DATE_TIME_FORMAT
            ) : null,
            'isNew' => false,
            'markAsTopseller' => $product->getMarkAsTopseller(),
            'purchasePrices' => null,
            'productNumber' => $product->getProductNumber(),
            'manufacturerId' => $product->getManufacturerId(),
            'taxId' => $product->getTaxId(),
            'tagIds' => $product->getTagIds(),
            'categoryIds' => $product->getCategoryTree(),
            'propertyIds' => $product->getPropertyIds(),
            'optionIds' => $product->getOptionIds(),
            'options' => $product->getVariation(),
        ];

        $lineItem->replacePayload($payload);

    }


    private function getNotCompleted(CartDataCollection $data, array $lineItems, bool $cartModified): array
    {
        $newLineItems = [];

        $areModified = array_filter($lineItems, function (LineItem $lineItem) {
            return $lineItem->isModified();
        });

        // If one Item is modified recalculate all.
        if (count($areModified) > 0) {
            return $lineItems;
        }

        // No items modified but one deleted
        if ($cartModified) {
            return $lineItems;
        }

        /** @var LineItem $lineItem */
        foreach ($lineItems as $lineItem) {
            $key = DynamicProductService::DYNAMIC_PRODUCT_LINE_ITEM_ID . $lineItem->getId();

            // check if some data is missing (label, price, cover)
            if (!$this->isComplete($lineItem)) {
                $newLineItems[] = $lineItem;
                continue;
            }

            // data already fetched?
            if ($data->has($key)) {
                continue;
            }
            $lineItems[] = $lineItem;
        }

        return $newLineItems;
    }

    private function isComplete(LineItem $lineItem): bool
    {
        return $lineItem->getPriceDefinition() !== null
            && $lineItem->getLabel() !== null
            && $lineItem->getDeliveryInformation() !== null
            && $lineItem->getQuantityInformation() !== null;
    }

    /**
     * @param SalesChannelContext $context
     * @param Cart $original
     */
    private function createCartIfNotExists(SalesChannelContext $context, Cart $original): void
    {
        try {
            $this->cartPersister->load($original->getToken(), $context);
        } catch (CartTokenNotFoundException $exception) {
            $this->cartPersister->save($original, $context);
        }
    }

    private function getProductName(array $payload): string
    {
        $event = $payload['event']['name'];
        $label = $payload['label']['name'];

//                if (strlen($label) >= 15) {
//                    $label =  substr($label, 0, 7). "..." . substr($label, -7);
//                }

        if (strlen($label) >= 23) {
            $label = substr($label, 0, 23) . "...";
        }

        $candy = $payload['candy']['name'];
        $package = $payload['eclm_package']['name'];
        $gramm = $payload['eclm_package']['gramm'];
        $label = "{$event}, \n{$label}, \n{$candy}, \n{$package}, {$gramm}g";

        return $label;
    }

    private function getLineItemCover(array $payload, SalesChannelContext $context): MediaEntity
    {
        $mediaId = $payload['eclm_package']['thumbnail']['mediaId'];

        /** @var MediaEntity $image */
        $image = $this->mediaRepository
            ->search(new Criteria([$mediaId]), $context->getContext())
            ->getEntities()
            ->first();
        return $image;
    }

    private function calculateAvailableStock(LineItem $lineItem, SalesChannelContext $context)
    {
        $sql = "SELECT
                	floor(min(calculated)) AS calculated
                FROM (
                	SELECT
                		min(available_stock),
                		sum( if(countable != 'non-countable', 1, 0)),
                   		sub_product_quantity,
                		min(available_stock) / (sub_product_quantity / line_item_quantity) / sum( if(countable != 'non-countable', 1, 0)) AS calculated
                	FROM (  
                	SELECT
                		p.available_stock - sum(sub_product_quantity) AS available_stock,
                		'non-countable' AS countable,
                		cp.*
                	FROM
                		ec_cart_product cp
                	LEFT JOIN product p ON cp.sub_product_id = p.id
                WHERE
                	cp.sub_product_id IN(
                	SELECT
                		cpsub.sub_product_id FROM ec_cart_product cpsub
                	WHERE
                		cpsub.line_item_id = :lineItemId and cpsub.token = :token) 
                    AND cp.line_item_id != :lineItemId 
                    AND cp.token = :token
                GROUP BY
                	cp.sub_product_id
                UNION
                SELECT
                	p.available_stock,
                	cp.sub_product_id AS countable,
                	cp.*
                FROM
                	ec_cart_product cp
                	LEFT JOIN product p ON cp.sub_product_id = p.id
                WHERE
                	cp.line_item_id = :lineItemId and cp.token = :token) AS group1
                    GROUP BY
                	sub_product_id) AS group2;";


        try {
            $result = $this->connection->fetchAssociative($sql, [
                'lineItemId' => $lineItem->getId(),
                'token' => $context->getToken()
            ]);
        } catch (Exception $e) {
            throw new ErrorException($e->getMessage());
        }
        return (int)$result['calculated'];
    }

}