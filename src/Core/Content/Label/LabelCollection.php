<?php
declare( strict_types=1 );

namespace EventCandy\LabelMe\Core\Content\Label;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add( LabelCollection $entity )
 * @method void              set( string $key, LabelCollection $entity )
 * @method LabelCollection[]    getIterator()
 * @method LabelCollection[]    getElements()
 * @method LabelCollection|null get( string $key )
 * @method LabelCollection|null first()
 * @method LabelCollection|null last()
 */
class LabelCollection extends EntityCollection {
    protected function getExpectedClass(): string {
        return LabelEntity::class;
    }
}
