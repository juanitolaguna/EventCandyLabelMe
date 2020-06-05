<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Core\Content\Label;

use EventCandy\LabelMe\Core\Content\Event\EventEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class LabelEntity extends Entity
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
     *
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var EventEntity|null
     */
    protected $event;




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


    public function getEvent(): ?EventEntity
    {
        return $this->event;
    }

    public function setEvent(EventEntity $event): void
    {
        $this->event = $event;
    }
}
