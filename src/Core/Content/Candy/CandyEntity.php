<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Candy;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CandyEntity extends Entity {
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $eurProMl;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( string $name ): void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getEurProMl(): int {
        return $this->eurProMl;
    }

    /**
     * @param int $eurProMl
     */
    public function setEurProMl( int $eurProMl ): void {
        $this->eurProMl = $eurProMl;
    }

    /**
     * @return MediaEntity|null
     */
    public function getMedia(): ?MediaEntity {
        return $this->media;
    }

    /**
     * @param MediaEntity|null $media
     */
    public function setMedia( ?MediaEntity $media ): void {
        $this->media = $media;
    }







}
