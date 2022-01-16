<?php
declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Candy;

use EventCandy\LabelMe\Core\Content\CandyPackage\CandyPackageDefinition;
use EventCandy\LabelMe\Core\Content\Package\PackageDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PriceField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CandyDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eclm_candy';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CandyCollection::class;
    }

    public function getEntityClass(): string
    {
        return CandyEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))
                ->addFlags(new Required(), new PrimaryKey()),

            (new StringField('name', 'name'))
                ->addFlags(new Required()),

            (new StringField('info_badge', 'infoBadge')),

            (new StringField('info_badge_color', 'infoBadgeColor')),

            (new LongTextField('product_data', 'productData'))
                ->addFlags(new AllowHtml()),

            (new BoolField('active', 'active')),

            (new BoolField('not_available', 'notAvailable')),

            (new IntField('position', 'position')),


            new FkField('media_id', 'mediaId', MediaDefinition::class),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            ),

            new ManyToManyAssociationField(
                'packages',
                PackageDefinition::class,
                CandyPackageDefinition::class,
                'candy_id',
                'package_id'
            ),

            new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                CandyPackageDefinition::class,
                'candy_id',
                'product_id'
            ),
        ]);
    }
}
