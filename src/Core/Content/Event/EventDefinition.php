<?php

namespace EventCandy\LabelMe\Core\Content\Event;

use EventCandy\LabelMe\Core\Content\Label\LabelDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class EventDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eclm_event';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return EventCollection::class;
    }

    public function getEntityClass(): string
    {
        return EventEntity::class;
    }

    protected function defineFields(): FieldCollection
    {

        return new FieldCollection([
            (new IdField('id', 'id'))
                ->addFlags(new Required(), new PrimaryKey()),

            (new StringField('name', 'name'))
                ->addFlags(new Required()),

            (new StringField('alternative_name', 'alternativeName')),

            (new BoolField('active', 'active')),

            ( new IntField( 'position', 'position' ) ),

            new FkField('media_id', 'mediaId', MediaDefinition::class),

            (new OneToManyAssociationField('labels', LabelDefinition::class, 'event_id', 'id'))->addFlags(new CascadeDelete()),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            )
        ]);
    }

}
