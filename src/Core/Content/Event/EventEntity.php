<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Event;

use EventCandy\LabelMe\Core\Content\Label\LabelCollection;
use EventCandy\LabelMe\Core\Content\Label\LabelEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class EventEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $alternativeName;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $notAvailable;

    /**
     *
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var LabelCollection|null
     */
    protected $labels;

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
    public function getActive(): bool
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

    public function getLabels(): ?LabelCollection
    {
//        file_put_contents('log.txt', 'get Labels', FILE_APPEND );
        return $this->labels;
    }

    public function setLabels(LabelCollection $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @return string|null
     */
    public function getAlternativeName(): ?string
    {
        return $this->alternativeName;
    }

    /**
     * @param string|null $alternativeName
     */
    public function setAlternativeName(?string $alternativeName): void
    {
        $this->alternativeName = $alternativeName;
    }

    /**
     * @return bool
     */
    public function getNotAvailable(): bool
    {
        return $this->notAvailable;
    }

    /**
     * @param bool $notAvailable
     */
    public function setNotAvailable(bool $notAvailable): void
    {
        $this->notAvailable = $notAvailable;
    }
}
