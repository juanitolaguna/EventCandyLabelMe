<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout\Cart\PriceDefinitionBuilder;

use Shopware\Core\Checkout\Cart\Price\Struct\PriceDefinitionInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface PriceDefinitionBuilderInterface {
    public function getPriceDefinition(ProductEntity $product, SalesChannelContext $context, int $quantity = 1): PriceDefinitionInterface;
}