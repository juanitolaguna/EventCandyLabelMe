<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart\PriceDefinitionBuilder;

use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\ReferencePriceDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\ReferencePriceDto;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\Unit\UnitCollection;

class QuantityPriceDefinitionBuilder implements PriceDefinitionBuilderInterface
{

    private EntityRepositoryInterface $unitRepository;

    private ?UnitCollection $units = null;

    private QuantityPriceCalculator $quantityPriceCalculator;

    /**
     * QuantityPriceDefinitionBuilder constructor.
     * @param EntityRepositoryInterface $unitRepository
     * @param UnitCollection|null $units
     * @param QuantityPriceCalculator $quantityPriceCalculator
     */
    public function __construct(
        EntityRepositoryInterface $unitRepository,
        QuantityPriceCalculator $quantityPriceCalculator
    )
    {
        $this->unitRepository = $unitRepository;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
    }


    public function getPriceDefinition(
        ProductEntity $product,
        SalesChannelContext $context,
        int $quantity = 1
    ): PriceDefinitionInterface
    {
        $units = $this->getUnits($context);
        $reference = ReferencePriceDto::createFromProduct($product);
        \assert($product->getPrice() !== null);

        $definition = $this->buildDefinition($product, $product->getPrice(), $context, $units, $reference);

        $price = $this->quantityPriceCalculator->calculate($definition, $context);

        return $this->buildPriceDefinition($price, $quantity);
    }

    private function getUnits(SalesChannelContext $context): UnitCollection
    {
        if ($this->units !== null) {
            return $this->units;
        }

        /** @var UnitCollection $units */
        $units = $this->unitRepository
            ->search(new Criteria(), $context->getContext())
            ->getEntities();

        return $this->units = $units;
    }

    private function buildDefinition(
        ProductEntity $product,
        ?PriceCollection $prices,
        SalesChannelContext $context,
        UnitCollection $units,
        ReferencePriceDto $reference,
        int $quantity = 1
    ): QuantityPriceDefinition
    {
        $price = $this->getPriceValue($prices, $context);

        \assert($product->getTaxId() !== null);
        $definition = new QuantityPriceDefinition($price, $context->buildTaxRules($product->getTaxId()), $quantity);
        $definition->setReferencePriceDefinition(
            $this->buildReferencePriceDefinition($reference, $units)
        );
        $definition->setListPrice(
            $this->getListPrice($prices, $context)
        );

        return $definition;

    }

    private function getPriceValue(?PriceCollection $prices, SalesChannelContext $context): float
    {
        /** @var Price $currency */
        $currency = $prices->getCurrencyPrice($context->getCurrencyId());

        $value = $this->getPriceForTaxState($currency, $context);

        if ($currency->getCurrencyId() !== $context->getCurrency()->getId()) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function getPriceForTaxState(Price $price, SalesChannelContext $context): float
    {
        if ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            return $price->getGross();
        }

        return $price->getNet();
    }

    private function buildReferencePriceDefinition(
        ReferencePriceDto $definition,
        UnitCollection $units
    ): ?ReferencePriceDefinition
    {
        if ($definition->getPurchase() === null || $definition->getPurchase() <= 0) {
            return null;
        }
        if ($definition->getUnitId() === null) {
            return null;
        }
        if ($definition->getReference() === null || $definition->getReference() <= 0) {
            return null;
        }
        if ($definition->getPurchase() === $definition->getReference()) {
            return null;
        }

        $unit = $units->get($definition->getUnitId());
        if ($unit === null) {
            return null;
        }

        return new ReferencePriceDefinition(
            $definition->getPurchase(),
            $definition->getReference(),
            $unit->getTranslation('name')
        );
    }

    private function getListPrice(?PriceCollection $prices, SalesChannelContext $context): ?float
    {
        if (!$prices) {
            return null;
        }

        $price = $prices->getCurrencyPrice($context->getCurrency()->getId());
        if ($price === null || $price->getListPrice() === null) {
            return null;
        }

        $value = $this->getPriceForTaxState($price->getListPrice(), $context);

        if ($price->getCurrencyId() !== $context->getCurrency()->getId()) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function buildPriceDefinition(
        CalculatedPrice $price,
        int $quantity
    ): QuantityPriceDefinition
    {
        $definition = new QuantityPriceDefinition($price->getUnitPrice(), $price->getTaxRules(), $quantity);
        if ($price->getListPrice() !== null) {
            $definition->setListPrice($price->getListPrice()->getPrice());
        }

        if ($price->getReferencePrice() !== null) {
            $definition->setReferencePriceDefinition(
                new ReferencePriceDefinition(
                    $price->getReferencePrice()->getPurchaseUnit(),
                    $price->getReferencePrice()->getReferenceUnit(),
                    $price->getReferencePrice()->getUnitName()
                )
            );
        }

        return $definition;
    }


}