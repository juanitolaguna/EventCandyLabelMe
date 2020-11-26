<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Package;

use EventCandy\LabelMe\Core\Content\Candy\CandyCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PackageEntity extends Entity
{
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
     * @var integer
     */
    protected $milliliters;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int|null
     */
    protected $cssSize;

    /**
     * @var string|null
     */
    protected $packageType;

    /**
     * @return string|null
     */
    public function getPackageType(): ?string
    {
        return $this->packageType;
    }

    /**
     * @param string|null $packageType
     */
    public function setPackageType(?string $packageType): void
    {
        $this->packageType = $packageType;
    }

    /**
     * @return int|null
     */
    public function getCssSize(): ?int
    {
        return $this->cssSize;
    }

    /**
     * @param int|null $cssSize
     */
    public function setCssSize(?int $cssSize): void
    {
        $this->cssSize = $cssSize;
    }

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
     * @return int
     */
    public function getMilliliters(): int
    {
        return $this->milliliters;
    }

    /**
     * @param int $milliliters
     */
    public function setMilliliters(int $milliliters): void
    {
        $this->milliliters = $milliliters;
    }

    /**
     *
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var CandyCollection|null
     */
    protected $candies;

    /**
     * @var ProductCollection|null
     */
    protected $products;

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
     * @return CandyCollection|null
     */
    public function getCandies(): ?CandyCollection
    {
        return $this->candies;
    }

    /**
     * @param CandyCollection|null $candies
     */
    public function setCandies(?CandyCollection $candies): void
    {
        $this->candies = $candies;
    }



    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
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
