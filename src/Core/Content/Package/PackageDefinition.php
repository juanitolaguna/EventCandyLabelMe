<?php

namespace EventCandy\LabelMe\Core\Content\Package;

use EventCandy\LabelMe\Core\Content\Candy\CandyDefinition;
use EventCandy\LabelMe\Core\Content\CandyPackage\CandyPackageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PackageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eclm_package';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return PackageCollection::class;
    }

    public function getEntityClass(): string
    {
        return PackageEntity::class;
    }

    protected function defineFields(): FieldCollection
    {

        return new FieldCollection([
            (new IdField('id', 'id'))
                ->addFlags(new Required(), new PrimaryKey()),

            (new StringField('name', 'name'))
                ->addFlags(new Required()),

            (new BoolField('active', 'active')),

            new FkField('media_id', 'mediaId', MediaDefinition::class),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            ),

            new ManyToManyAssociationField(
                'candies',
                CandyDefinition::class,
                CandyPackageDefinition::class,
                'package_id',
                'candy_id'
            ),
        ]);
    }

}
