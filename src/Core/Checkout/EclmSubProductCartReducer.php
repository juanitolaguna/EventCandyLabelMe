<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Checkout;

use EventCandy\LabelMe\Core\Checkout\Cart\EclmCartProcessor;
use EventCandy\Sets\Core\Checkout\Cart\SubProductQuantityInCartReducerInterface;
use EventCandyCandyBags\Core\Checkout\Cart\CandyBagsCartProcessor;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class EclmSubProductCartReducer implements SubProductQuantityInCartReducerInterface
{

    public function reduce(Cart $cart, string $relatedMainId, string $mainProductId, int $subProductQuantity, SalesChannelContext $context = null): int
    {
        // TODO: Implement reduce() method.
        $lineItems = $cart->getLineItems()->filterFlatByType(EclmCartProcessor::TYPE);
        if (count($lineItems) == 0) {
            return 0;
        }

        $baseLineItemId = null;
        if ($context && $context->getExtension('lineItem')) {
            /** @var LineItem $baseLineItem */
            $baseLineItem = $context->getExtension('lineItem');
            $baseLineItemId = $baseLineItem ? $baseLineItem->getId() : null;
        }

        $counter = 0;

        foreach ($lineItems as $lineItem) {
            if ($baseLineItemId === $lineItem->getId()) {
                continue;
            }

            $lineItemProductId = $lineItem->getPayload()['eclm_package']['product']['id'];
            if ($lineItemProductId === $relatedMainId) {
                $counter += $lineItem->getQuantity() * $subProductQuantity;
            }
        }

        return $counter;
    }
}