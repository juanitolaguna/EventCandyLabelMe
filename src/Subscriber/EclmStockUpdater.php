<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Subscriber;

use Doctrine\DBAL\Connection;
use EventCandy\LabelMe\Core\Checkout\Cart\EclmCartProcessor;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EclmStockUpdater implements EventSubscriberInterface
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ProductDefinition
     */
    private $definition;

    /**
     * @var CacheClearer
     */
    private $cache;

    /**
     * @var EntityCacheKeyGenerator
     */
    private $cacheKeyGenerator;

    public function __construct(
        Connection $connection,
        ProductDefinition $definition,
        CacheClearer $cache,
        EntityCacheKeyGenerator $cacheKeyGenerator
    )
    {
        $this->connection = $connection;
        $this->definition = $definition;
        $this->cache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'orderPlaced',
        ];
    }

    public function orderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $ids = [];
        foreach ($event->getOrder()->getLineItems() as $lineItem) {
            if ($lineItem->getType() !== EclmCartProcessor::TYPE) {
                continue;
            }
            $ids[] = $lineItem->getReferencedId();
        }

        $this->update($ids, $event->getContext());

        $this->clearCache($ids);
    }

    public function update(array $ids, Context $context): void
    {
        if ($context->getVersionId() !== Defaults::LIVE_VERSION) {
            return;
        }

        $this->updateAvailableStock($ids, $context);

        $this->updateAvailableFlag($ids, $context);
    }

    private function clearCache(array $ids): void
    {
        $tags = [];
        foreach ($ids as $id) {
            $tags[] = $this->cacheKeyGenerator->getEntityTag($id, $this->definition->getEntityName());
        }

        $tags[] = $this->cacheKeyGenerator->getFieldTag($this->definition, 'id');
        $tags[] = $this->cacheKeyGenerator->getFieldTag($this->definition, 'available');
        $tags[] = $this->cacheKeyGenerator->getFieldTag($this->definition, 'availableStock');
        $tags[] = $this->cacheKeyGenerator->getFieldTag($this->definition, 'stock');

        $this->cache->invalidateTags($tags);
    }

    private function updateAvailableStock(array $ids, Context $context): void
    {
        $ids = array_filter(array_keys(array_flip($ids)));

        if (empty($ids)) {
            return;
        }

        $bytes = Uuid::fromHexToBytesList($ids);

        $sql = '
UPDATE product SET available_stock = stock - (
    SELECT IFNULL(SUM(order_line_item.quantity), 0)

    FROM order_line_item
        INNER JOIN `order`
            ON `order`.id = order_line_item.order_id
            AND `order`.version_id = order_line_item.order_version_id
        INNER JOIN state_machine_state
            ON state_machine_state.id = `order`.state_id
            AND state_machine_state.technical_name NOT IN (:states)

    WHERE LOWER(order_line_item.referenced_id) = LOWER(HEX(product.id))
    AND order_line_item.type = :type
    AND order_line_item.version_id = :version
)
WHERE product.id IN (:ids) AND product.version_id = :version;
        ';

        RetryableQuery::retryable(function () use ($sql, $bytes, $context): void {
            $this->connection->executeUpdate(
                $sql,
                [
                    'type' => EclmCartProcessor::TYPE,
                    'version' => Uuid::fromHexToBytes($context->getVersionId()),
                    'states' => [OrderStates::STATE_COMPLETED, OrderStates::STATE_CANCELLED],
                    'ids' => $bytes,
                ],
                [
                    'ids' => Connection::PARAM_STR_ARRAY,
                    'states' => Connection::PARAM_STR_ARRAY,
                ]
            );
        });
    }

    private function updateAvailableFlag(array $ids, Context $context): void
    {
        $ids = array_filter(array_keys(array_flip($ids)));

        if (empty($ids)) {
            return;
        }

        $bytes = Uuid::fromHexToBytesList($ids);

        $sql = '
            UPDATE product
            LEFT JOIN product parent
                ON parent.id = product.parent_id
                AND parent.version_id = product.version_id

            SET product.available = IFNULL((
                IFNULL(product.is_closeout, parent.is_closeout) * product.available_stock
                >=
                IFNULL(product.is_closeout, parent.is_closeout) * IFNULL(product.min_purchase, parent.min_purchase)
            ), 0)
            WHERE product.id IN (:ids)
            AND product.version_id = :version
        ';

        RetryableQuery::retryable(function () use ($sql, $context, $bytes): void {
            $this->connection->executeUpdate(
                $sql,
                ['ids' => $bytes, 'version' => Uuid::fromHexToBytes($context->getVersionId())],
                ['ids' => Connection::PARAM_STR_ARRAY]
            );
        });
    }

}
