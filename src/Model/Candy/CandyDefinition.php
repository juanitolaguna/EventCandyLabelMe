<?php declare( strict_types=1 );

namespace EventCandyLabelMe\Model\Candy;

use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Country\CountryDefinition;

class CandyDefinition extends EntityDefinition {
    public const ENTITY_NAME = 'eclm_candy';

    public function getEntityName(): string {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string {
        return CandyCollection::class;
    }

    public function getEntityClass(): string {
        return CandyEntity::class;
    }

    protected function defineFields(): FieldCollection {

        return new FieldCollection( [
            ( new IdField( 'id', 'id' ) )
                ->addFlags( new Required(), new PrimaryKey() ),

            ( new StringField( 'name', 'name' ) )
                ->addFlags( new Required() ),

            ( new IntField( 'eur_pro_ml', 'eurProMl' ) )
                ->addFlags( new Required() ),


            new FkField( 'media_id', 'mediaId', MediaDefinition::class ),

            new ManyToOneAssociationField(
                'media',
                'media_id',
                MediaDefinition::class
            )
        ] );
    }
}
