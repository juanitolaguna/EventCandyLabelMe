<?php
declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Event;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(EventCollection $entity)
 * @method void              set(string $key, EventCollection $entity)
 * @method EventCollection[]    getIterator()
 * @method EventCollection[]    getElements()
 * @method EventCollection|null get(string $key)
 * @method EventCollection|null first()
 * @method EventCollection|null last()
 */
class EventCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return EventEntity::class;
    }
}
