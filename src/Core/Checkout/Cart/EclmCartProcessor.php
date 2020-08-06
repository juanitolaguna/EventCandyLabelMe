<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart;

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
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\ProductPriceDefinitionBuilderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
     */
    public function __construct(
        LoggerInterface $logger,
        PercentagePriceCalculator $percentagePriceCalculator,
        AbsolutePriceCalculator $absolutePriceCalculator,
        QuantityPriceCalculator $quantityPriceCalculator,
        EntityRepositoryInterface $mediaRepository,
        EntityRepositoryInterface $productRepository,
        ProductPriceDefinitionBuilderInterface $priceDefinitionBuilder

    )
    {
        $this->logger = $logger;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
        $this->priceDefinitionBuilder = $priceDefinitionBuilder;
    }


    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {

        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }

//        $this->logger->log(100, 'collect label-me');

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
                $event = $payload['event']['name'];
                $label = $payload['label']['name'];
                $candy = $payload['candy']['name'];
                $package = $payload['eclm_package']['name'];
                $label = "{$event} | {$label} | {$candy} | {$package}";
                $item->setLabel($label);
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


            $item->setStackable(true)
                ->setRemovable(true)
                ->setDeliveryInformation(
                    new DeliveryInformation(
                        $product->getStock(),
                        (float)$product->getWeight(),
                        (bool)$product->getShippingFree(),
                        $product->getRestockTime(),
                        $deliveryTime
                    ))
                ->setQuantityInformation(new QuantityInformation());
        }
    }


    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }

        foreach ($eclmItems as $item) {
            $priceDefinition = $item->getPriceDefinition();
            if ($priceDefinition === null || !$priceDefinition instanceof QuantityPriceDefinition) {
                throw new \RuntimeException(sprintf('Product "%s" has invalid price definition', $item->getLabel()));
            }

            $item->setPrice( $this->quantityPriceCalculator->calculate($priceDefinition, $context));
            $toCalculate->add($item);
        }


    }

}
