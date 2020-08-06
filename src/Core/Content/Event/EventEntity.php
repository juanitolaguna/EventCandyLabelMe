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
     * @var bool
     */
    protected $active;

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

    public function getLabels(): ?LabelCollection
    {
//        file_put_contents('log.txt', 'get Labels', FILE_APPEND );
        return $this->labels;
    }

    public function setLabels(LabelCollection $labels): void
    {
        $this->labels = $labels;
    }
}
