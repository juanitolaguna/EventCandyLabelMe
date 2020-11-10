<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart;

use Doctrine\DBAL\Connection;
use ErrorException;
use EventCandy\Sets\Storefront\Page\Product\Subscriber\ProductListingSubscriber;
use EventCandy\Sets\Utils;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Cart\ProductStockReachedError;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\ProductPriceDefinitionBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;

class EclmCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

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
    private $productRepository;

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


    public const TYPE = 'event-candy-label-me';
    public const DATA_KEY = 'eclm-';


    /**
     * EclmCartProcessor constructor.
     * @param LoggerInterface $logger
     * @param PercentagePriceCalculator $percentagePriceCalculator
     * @param AbsolutePriceCalculator $absolutePriceCalculator
     * @param QuantityPriceCalculator $quantityPriceCalculator
     * @param EntityRepositoryInterface $mediaRepository
     * @param EntityRepositoryInterface $productRepository
     * @param ProductPriceDefinitionBuilderInterface $priceDefinitionBuilder
     * @param Connection $connection
     * @param ProductListingSubscriber $productListingSubscriber
     */
    public function __construct(
        LoggerInterface $logger,
        PercentagePriceCalculator $percentagePriceCalculator,
        AbsolutePriceCalculator $absolutePriceCalculator,
        QuantityPriceCalculator $quantityPriceCalculator,
        EntityRepositoryInterface $mediaRepository,
        EntityRepositoryInterface $productRepository,
        ProductPriceDefinitionBuilderInterface $priceDefinitionBuilder,
        Connection $connection,
        ProductListingSubscriber $productListingSubscriber

    )
    {
        $this->logger = $logger;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->priceDefinitionBuilder = $priceDefinitionBuilder;
        $this->connection = $connection;
        $this->productListingSubscriber = $productListingSubscriber;
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

            /** @var ProductEntity $product */
            $product = $this->productRepository
                ->search(new Criteria([$productId]), $context->getContext())->first();


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

            $item->setDescription('This is dummy description!');

            $deliveryTime = $product->getDeliveryTime();
            if ($deliveryTime !== null) {
                $deliveryTime = DeliveryTime::createFromEntity($deliveryTime);
            }

            if (($product !== null) && ($product->getAvailableStock() !== null)) {

                $keyIsTrue = array_key_exists('ec_is_set', $product->getCustomFields())
                    && $product->getCustomFields()['ec_is_set'];

                if ($keyIsTrue) {
                    $stock = $this->productListingSubscriber->getAvailableStock($product->getId(), $context->getContext());
                } else {
                    $stock = $product->getAvailableStock();
                }
            }

            $minPurchase = $product->getMinPurchase() ?? 1;
            $purchaseSteps = $product->getPurchaseSteps() ?? 1;

            $quantityInformation = new QuantityInformation();
            $quantityInformation
                ->setMaxPurchase($stock)
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

            $this->addRelatedProductsToPayload($item);
        }


    }

    private function addRelatedProductsToPayload(LineItem $lineItem)
    {
        $sqlSetProducts = 'select product_id, product_version_id, quantity from ec_product_product as pp
                    where pp.set_product_id = :id;';

        $rows = $this->connection->fetchAll(
            $sqlSetProducts,
            ['id' => Uuid::fromHexToBytes($lineItem->getReferencedId())]
        );

        $setProducts = [];
        foreach ($rows as $row) {
            $setProducts[] = [
                'product_id' => Uuid::fromBytesToHex($row['product_id']),
                'product_version_id' => Uuid::fromBytesToHex($row['product_version_id']),
                'quantity' => $row['quantity']
            ];
        }

        $lineItem->setPayload([self::TYPE => $setProducts]);
    }


    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }


        foreach ($eclmItems as $item) {
            $payload = $item->getPayload()['eclm_package'];

            $availableStock = $this->productListingSubscriber->getAvailableStock($payload['product']['id'], $context->getContext());

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




            $priceDefinition = $item->getPriceDefinition();
            if ($priceDefinition === null || !$priceDefinition instanceof QuantityPriceDefinition) {
                throw new \RuntimeException(sprintf('Product "%s" has invalid price definition', $item->getLabel()));
            }

            $item->setPrice($this->quantityPriceCalculator->calculate($priceDefinition, $context));
            $toCalculate->add($item);
        }

    }

    private function getProductName(array $payload) :string
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
        return (int) (floor(($current - $min) / $steps) * $steps + $min);
    }

}
