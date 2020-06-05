<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\CandyPackage;

use EventCandy\LabelMe\Core\Content\Candy\CandyEntity;
use EventCandy\LabelMe\Core\Content\Package\PackageEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CandyPackageEntity extends Entity
{
    use EntityIdTrait;

    /**
     *
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var PackageEntity|null
     */
    protected $package;


    /**
     * @var CandyEntity|null
     */
    protected $candy;

    /**
     * @return PackageEntity|null
     */
    public function getPackage(): ?PackageEntity
    {
        return $this->package;
    }

    /**
     * @param PackageEntity|null $package
     */
    public function setPackage(?PackageEntity $package): void
    {
        $this->package = $package;
    }

    /**
     * @return CandyEntity|null
     */
    public function getCandy(): ?CandyEntity
    {
        return $this->candy;
    }

    /**
     * @param CandyEntity|null $candy
     */
    public function setCandy(?CandyEntity $candy): void
    {
        $this->candy = $candy;
    }



    /**
     * @return MediaEntity|null
     */
    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    /**
     * @param MediaEntity|null $media
     */
    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

}
