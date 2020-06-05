<?php
declare( strict_types=1 );

namespace EventCandy\LabelMe\Core\Content\CandyPackage;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add( CandyPackageCollection $entity )
 * @method void              set( string $key, CandyPackageCollection $entity )
 * @method CandyPackageCollection[]    getIterator()
 * @method CandyPackageCollection[]    getElements()
 * @method CandyPackageCollection|null get( string $key )
 * @method CandyPackageCollection|null first()
 * @method CandyPackageCollection|null last()
 */
class CandyPackageCollection extends EntityCollection {
    protected function getExpectedClass(): string {
        return CandyPackageEntity::class;
    }
}
