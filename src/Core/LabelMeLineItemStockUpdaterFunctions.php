<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core;

use EventCandy\LabelMe\Core\Checkout\Cart\EclmCartProcessor;
use EventCandy\Sets\Core\Content\Product\DataAbstractionLayer\LineItemStockUpdaterFunctionsInterface;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Framework\Uuid\Uuid;

class LabelMeLineItemStockUpdaterFunctions implements LineItemStockUpdaterFunctionsInterface
{

    public function getLineItemType(): string
    {
        return EclmCartProcessor::TYPE;
    }

    public function createOrderLineItemProducts(OrderLineItemEntity $lineItem, CheckoutOrderPlacedEvent $event): array
    {
        // TODO: Implement createOrderLineItemProducts() method.

        $order = $event->getOrder();
        $lineItemQuantity = $lineItem->getQuantity();

        $orderLineItems = [];

        // Main Product
        $mainOrderLineItemId = Uuid::randomHex();
        $orderLineItems[] = [
            'id' => $mainOrderLineItemId,
            'productId' => $lineItem->getReferencedId(),
            'orderId' => $order->getId(),
            'orderLineItemId' => $lineItem->getId(),
            'quantity' => $lineItemQuantity
        ];


        $subProducts = $lineItem->getPayload()[$this->getLineItemType()];

        foreach ($subProducts as $product) {
            $orderLineItems[] = [
                'id' => Uuid::randomHex(),
                'parentId' => $mainOrderLineItemId,
                'productId' => $product['product_id'],
                'orderId' => $order->getId(),
                'orderLineItemId' => $lineItem->getId(),
                'quantity' => $product['quantity'] * $lineItemQuantity
            ];
        }

        return $orderLineItems;
    }
}