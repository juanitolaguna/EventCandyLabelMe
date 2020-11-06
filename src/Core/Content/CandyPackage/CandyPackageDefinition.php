<?php

namespace EventCandy\LabelMe\Core\Content\CandyPackage;

use EventCandy\LabelMe\Core\Content\Candy\CandyDefinition;
use EventCandy\LabelMe\Core\Content\Package\PackageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class CandyPackageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eclm_candy_package';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CandyPackageCollection::class;
    }

    public function getEntityClass(): string
    {
        return CandyPackageEntity::class;
    }

    protected function defineFields(): FieldCollection
    {

        return new FieldCollection([
            ( new IdField( 'id', 'id' ) )
                ->addFlags( new Required(), new PrimaryKey()),
            (new FkField('candy_id', 'candyId', CandyDefinition::class))
                ->addFlags(new Required() ),
            (new FkField('package_id', 'packageId', PackageDefinition::class))
                ->addFlags(new Required() ),

            (new FkField('media_id', 'mediaId', MediaDefinition::class)),

            (new FkField('product_id', 'productId', ProductDefinition::class)),

            ( new IntField( 'gramm', 'gramm' ) ),

            new ManyToOneAssociationField('candy', 'candy_id', CandyDefinition::class),
            new ManyToOneAssociationField('package', 'package_id', PackageDefinition::class),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            ),

            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class
            ),

            new CreatedAtField()
        ]);
    }

}
