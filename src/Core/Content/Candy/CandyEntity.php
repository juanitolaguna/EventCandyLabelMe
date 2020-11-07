<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Candy;

use EventCandy\LabelMe\Core\Content\Package\PackageCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CandyEntity extends Entity {
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;


    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var PackageCollection|null
     */
    protected $packages;

    /**
     * @var ProductCollection|null
     */
    protected $products;


    /**
     * @var int
     */
    protected $position;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }


    /**
     * @return ProductCollection|null
     */
    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }

    /**
     * @param ProductCollection|null $products
     */
    public function setProducts(?ProductCollection $products): void
    {
        $this->products = $products;
    }

    /**
     * @return PackageCollection|null
     */
    public function getPackages(): ?PackageCollection
    {
        return $this->packages;
    }

    /**
     * @param PackageCollection|null $packages
     */
    public function setPackages(?PackageCollection $packages): void
    {
        $this->packages = $packages;
    }
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
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
