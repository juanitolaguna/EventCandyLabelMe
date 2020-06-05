<?php

namespace EventCandy\LabelMe\Core\Content\Label;

use EventCandy\LabelMe\Core\Content\Event\EventDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class LabelDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'eclm_label';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return LabelCollection::class;
    }

    public function getEntityClass(): string
    {
        return LabelEntity::class;
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

            (new FkField('event_id', 'eventId', EventDefinition::class))->addFlags(new Required()),

            new ManyToOneAssociationField(
                'event',
                'event_id',
                EventDefinition::class
            ),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            )
        ]);
    }

}
