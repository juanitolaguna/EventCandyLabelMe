<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart;

use EventCandy\LabelMe\Core\Content\CandyPackage\CandyPackageDefinition;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
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
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

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


    public const TYPE = 'event-candy-label-me';
    public const DATA_KEY = 'eclm-';


    /**
     * EclmCartProcessor constructor.
     * @param LoggerInterface $logger
     * @param PercentagePriceCalculator $percentagePriceCalculator
     * @param AbsolutePriceCalculator $absolutePriceCalculator
     * @param QuantityPriceCalculator $quantityPriceCalculator
     * @param EntityRepositoryInterface $mediaRepository
     */
    public function __construct(
        LoggerInterface $logger,
        PercentagePriceCalculator $percentagePriceCalculator,
        AbsolutePriceCalculator $absolutePriceCalculator,
        QuantityPriceCalculator $quantityPriceCalculator,
        EntityRepositoryInterface $mediaRepository
    )
    {
        $this->logger = $logger;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
        $this->mediaRepository = $mediaRepository;
    }


    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }


        foreach ($eclmItems as $item) {
            $item->setPrice(new CalculatedPrice(
                100,
                100,
                new CalculatedTaxCollection(),
                new TaxRuleCollection()
            ));
            $toCalculate->add($item);
        }


    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {

        $eclmItems = $original->getLineItems()->filterType(self::TYPE);

        if (\count($eclmItems) === 0) {
            return;
        }

//        $cdata = $original->getLineItems()->getPayload();
//        $key = key($cdata);

//        foreach ($eclmItems as $item) {
//            $data->set(self::DATA_KEY . $item->getId(), $eclmItems);
//        }

        foreach ($eclmItems as $item) {

            $mediaId = $item->getPayload()['eclm_package']['thumbnail']['mediaId'];

            $this->logger->log(100, 'Media Id: '. $mediaId);

            /** @var MediaEntity $image */
            $image = $this->mediaRepository
                ->search(new Criteria([$mediaId]), $context->getContext())
                ->getEntities()
                ->first();

            $this->logger->log(100, 'Image: ', [$image->getUrl()]);

            $item->setCover($image);
            $item->setDescription("Text \n Und mehr text");
            $item->setGood(false);

            $this->enrichlineItem($item);
//            $this->addLineItemInfo($item);
//            $this->addDiscount()

//            $this->logger->log(100, 'Cart Data', [$item->getPayload()['event']['name']]);
        }

//        if (array_key_exists($key, $cdata)) {
//            $this->logger->log(100, 'Cart Data', ['event']);
//            $payload = $cdata[$key];
//            $data->set(self::DATA_KEY . $payload['event']['id'] , $payload );
//        }


    }

    private function enrichlineItem(LineItem $lineitem): void
    {
        $lineitem->setRemovable(true)
            ->setStackable(true)
            ->setDeliveryInformation(
                new DeliveryInformation(100, 100, false))
            ->setQuantityInformation(new QuantityInformation());

    }

}
