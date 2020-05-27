<?php
declare( strict_types=1 );

namespace EventCandy\LabelMe\Core\Content\Candy;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add( CandyCollection $entity )
 * @method void              set( string $key, CandyCollection $entity )
 * @method CandyCollection[]    getIterator()
 * @method CandyCollection[]    getElements()
 * @method CandyCollection|null get( string $key )
 * @method CandyCollection|null first()
 * @method CandyCollection|null last()
 */
class CandyCollection extends EntityCollection {
    protected function getExpectedClass(): string {
        return CandyEntity::class;
    }
}
