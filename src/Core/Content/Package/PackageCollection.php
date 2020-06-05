<?php
declare( strict_types=1 );

namespace EventCandy\LabelMe\Core\Content\Package;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add( PackageCollection $entity )
 * @method void              set( string $key, PackageCollection $entity )
 * @method PackageCollection[]    getIterator()
 * @method PackageCollection[]    getElements()
 * @method PackageCollection|null get( string $key )
 * @method PackageCollection|null first()
 * @method PackageCollection|null last()
 */
class PackageCollection extends EntityCollection {
    protected function getExpectedClass(): string {
        return PackageEntity::class;
    }
}
