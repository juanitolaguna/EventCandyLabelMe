<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core;

use EventCandy\LabelMe\Core\Checkout\Cart\LabelMeCartCollector;
use EventCandy\Sets\Core\Content\Product\DataAbstractionLayer\SetProductLineItemStockUpdaterFunctions;

class LabelMeLineItemStockUpdaterFunctions extends SetProductLineItemStockUpdaterFunctions
{

    public function getLineItemType(): string
    {
        return LabelMeCartCollector::TYPE;
    }
}