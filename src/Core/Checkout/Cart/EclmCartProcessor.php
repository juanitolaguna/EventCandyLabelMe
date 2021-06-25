<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart;

use Doctrine\DBAL\Connection;
use EventCandy\Sets\Core\Checkout\Cart\SetProductCartProcessor;
use EventCandy\Sets\Core\SetProductLoadedEvent;
use EventCandy\Sets\Storefront\Page\Product\Subscriber\ProductListingSubscriber;
use EventCandyCandyBags\Core\Checkout\Cart\CandyBagsCartProcessor;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\ProductPriceDefinitionBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EclmCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
{

    /**
     * @var QuantityPriceCalculator
     */
    private $quantityPriceCalculator;

    /**
     * @var PercentagePriceCalculator
     */
    private $percentagePriceCalculator;

    /**
     * @var AbsolutePriceCalculator
     */
    private $absolutePriceCalculator;

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $repository;

    /**
     * @var SalesChannelRepository
     */
    private $salesChannelRepository;

    /**
     * @var ProductPriceDefinitionBuilderInterface
     */
    private $priceDefinitionBuilder;


    /** @var Connection */
    private $connection;

    /**
     * Contains available Stock Calculation method.
     * @var ProductListingSubscriber
     */
    private $productListingSubscriber;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public const TYPE = 'event-candy-label-me';
    public const DATA_KEY = 'eclm-';

    /**
     * EclmCartProcessor constructor.
     * @param QuantityPriceCalculator $quantityPriceCalculator
     * @param PercentagePriceCalculator $percentagePriceCalculator
     * @param AbsolutePriceCalculator $absolutePriceCalculator
     * @param EntityRepositoryInterface $mediaRepository
     * @param EntityRepositoryInterface $repository
     * @param SalesChannelRepository $salesChannelRepository
     * @param ProductPriceDefinitionBuilderInterface $priceDefinitionBuilder
     * @param Connection $connection
     * @param ProductListingSubscriber $productListingSubscriber
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(QuantityPriceCalculator $quantityPriceCalculator, PercentagePriceCalculator $percentagePriceCalculator, AbsolutePriceCalculator $absolutePriceCalculator, EntityRepositoryInterface $mediaRepository, EntityRepositoryInterface $repository, SalesChannelRepository $salesChannelRepository, ProductPriceDefinitionBuilderInterface $priceDefinitionBuilder, Connection $connection, ProductListingSubscriber $productListingSubscriber, EventDispatcherInterface $eventDispatcher)
    {
        $this->quantityPriceCalculator = $quantityPriceCalculator;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->mediaRepository = $mediaRepository;
        $this->repository = $repository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->priceDefinitionBuilder = $priceDefinitionBuilder;
        $this->connection = $connection;
        $this->productListingSubscriber = $productListingSubscriber;
        $this->eventDispatcher = $eventDispatcher;
    }


    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {

        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }

        /** @var LineItem $item */
        foreach ($eclmItems as $item) {

            $payload = $item->getPayload();

            $productId = $payload['eclm_package']['product']['id'];
            $item->setReferencedId($productId);


            $criteria = new Criteria([$productId]);

            /** @var ProductEntity[] $result */
            $result = $this->repository->search($criteria, $context->getContext())->getElements();

            $context->addExtension('lineItem', $item);
            $event = new SetProductLoadedEvent($context, $result);
            $this->eventDispatcher->dispatch($event);
            $context->removeExtension('lineItem');
            $product = $result[$productId];

            $item->setPayload(['productNumber' => $product->getProductNumber()]);
            $data->set(self::DATA_KEY . $productId, $product);

            $prices = $this->priceDefinitionBuilder->build($product, $context, $item->getQuantity());
            $item->setPriceDefinition($prices->getQuantityPrice());

            //setLabel
            if (!$item->getLabel()) {
                $item->setLabel($this->getProductName($payload));
            }

            //set image
            if (!$item->getCover()) {
                $mediaId = $payload['eclm_package']['thumbnail']['mediaId'];
                /** @var MediaEntity $image */
                $image = $this->mediaRepository
                    ->search(new Criteria([$mediaId]), $context->getContext())
                    ->getEntities()
                    ->first();
                $item->setCover($image);
            }


            $item->setDescription('');

            $deliveryTime = $product->getDeliveryTime();
            if ($deliveryTime !== null) {
                $deliveryTime = DeliveryTime::createFromEntity($deliveryTime);
            }

            // Product Subscriber should resolve
//            if (($product !== null) && ($product->getAvailableStock() !== null)) {
//
//                $keyIsTrue = array_key_exists('ec_is_set', $product->getCustomFields())
//                    && $product->getCustomFields()['ec_is_set'];
//
//                if ($keyIsTrue) {
//                    $stock = $this->productListingSubscriber->getAvailableStock($product->getId(), $context);
//                } else {
//                    $stock = $product->getAvailableStock();
//                }
//            }

            $minPurchase = $product->getMinPurchase() ?? 1;
            $purchaseSteps = $product->getPurchaseSteps() ?? 1;

            $quantityInformation = new QuantityInformation();
            $quantityInformation
                ->setMaxPurchase($product->getAvailableStock())
                ->setMinPurchase($minPurchase)
                ->setPurchaseSteps($purchaseSteps);


            $item->setRemovable(true)
                ->setDeliveryInformation(
                    new DeliveryInformation(
                        $product->getStock(),
                        (float)$product->getWeight(),
                        (bool)$product->getShippingFree(),
                        $product->getRestockTime(),
                        $deliveryTime
                    ))
                ->setQuantityInformation($quantityInformation);

            $this->addRelatedProductsToPayload($item, $context);
        }


    }

    /**
     * #dup - @link SetProductCartProcessor
     * #dup - @link CandyBagsCartProcessor
     * @param LineItem $lineItem
     * @param SalesChannelContext $context
     */
    private function addRelatedProductsToPayload(LineItem $lineItem, SalesChannelContext $context)
    {
        $sqlSetProducts = 'select
                            	pp.product_version_id,
                            	pp.product_id,
                            	pp.quantity,
                            	pt.name,
                            	p.product_number
                            from
                            	ec_product_product as pp
                            	left join product_translation pt on pp.product_id = pt.product_id
                            	left join product p on pp.product_id = p.id
                            where
                            	pp.set_product_id = :id
                            	and pt.language_id = :languageId';

        $rows = $this->connection->fetchAll(
            $sqlSetProducts,
            [
                'id' => Uuid::fromHexToBytes($lineItem->getReferencedId()),
                'languageId' => Uuid::fromHexToBytes($context->getContext()->getLanguageId())
            ]
        );

        $setProducts = [];
        $lineItemSubProducts = "";

        foreach ($rows as $row) {
            $setProducts[] = [
                'product_number' => $row['product_number'],
                'name' => $row['name'],
                'product_id' => Uuid::fromBytesToHex($row['product_id']),
                'product_version_id' => Uuid::fromBytesToHex($row['product_version_id']),
                'quantity' => $row['quantity']
            ];

            $lineItemSubProducts .= "- {$row['product_number']} - {$row['name']} - {$row['quantity']}x \n";
        }

        $lineItem->setPayload([self::TYPE => $setProducts]);
        // format setProducts as a string
        $lineItem->setPayload(['line_item_sub_products' => $lineItemSubProducts]);

    }


    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }

        // ToDo: cleanup
//        $occurrences = [];
        //count same candy package products
//        foreach ($eclmItems as $item) {
//            $id = $item->getId();
//            $productId = $item->getPayload()['eclm_package']['product']['id'];
//            if (array_key_exists($productId, $occurrences)) {
//                $occurrences[$productId][] = [$id, $item->getQuantity(), $item->isModified()];
//            } else {
//                $occurrences[$productId] = [[$id, $item->getQuantity(), $item->isModified()]];
//            }
//        }

        foreach ($eclmItems as $item) {
            $payload = $item->getPayload()['eclm_package'];

            /** @var ProductEntity $product */
            $product = $data->get(self::DATA_KEY . $payload['product']['id']);
            $availableStock = $product->getAvailableStock();

            if ($payload['maximalQuantity']) {
                $availableStock = $availableStock > $payload['maximalQuantity'] ? $payload['maximalQuantity'] : $availableStock;
            }


            if ($availableStock < $item->getQuantity()) {
                $item->setQuantity($availableStock);
            }


            $fixedQuantity = $this->fixQuantity($payload['minimalQuantity'] ?? 1, $item->getQuantity(), $payload['purchaseSteps'] ?? 1);
            if ($item->getQuantity() !== $fixedQuantity) {
                $item->setQuantity($fixedQuantity);
            }

//            $sameProduct = count($occurrences[$payload['product']['id']]);


//            $possibleQuantity = floor($availableStock / $sameProduct);
//            $item->getQuantityInformation()->setMaxPurchase((int)$possibleQuantity);

//            if ($item->getQuantity() > $possibleQuantity) {
//                $item->setQuantity((int)$possibleQuantity);
//                $toCalculate->addErrors(
//                    new ProductStockReachedError($item->getId(), (string)$item->getLabel(), (int)$possibleQuantity)
//                );
//            }

            $priceDefinition = $item->getPriceDefinition();
            if ($priceDefinition === null || !$priceDefinition instanceof QuantityPriceDefinition) {
                throw new \RuntimeException(sprintf('Product "%s" has invalid price definition', $item->getLabel()));
            }

            $item->setPrice($this->quantityPriceCalculator->calculate($priceDefinition, $context));
            $toCalculate->add($item);
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

    private function fixQuantity(int $min, int $current, int $steps): int
    {
        return (int)(floor(($current - $min) / $steps) * $steps + $min);
    }

}
