<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;

class CandyMediaProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'media';
    }

    public function getPayload(): array
    {
        $folderId = $this->getPluginMediaFolderId('Event Candy');

        return [
            [
                'id' => 'c7ce0e396809439c8997ae68992924aa',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => 'b9c207ca62da47a1ac9019c475daaf02',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => 'c614ef6e428a4d7ab861ff8a2b98d2f3',
                'mediaFolderId' => $folderId
            ]
        ];
    }

    public function finalize(Context $context): void
    {
        foreach (glob(__DIR__ . '/../Resources/media/candy/*/*.png') as $file) {
            $this->fileSaver->persistFileToMedia(
                new MediaFile(
                    $file,
                    mime_content_type($file),
                    pathinfo($file, PATHINFO_EXTENSION),
                    filesize($file)
                ),
                pathinfo($file, PATHINFO_FILENAME),
                basename(dirname($file)),
                $context
            );
        }

        parent::finalize($context);
    }
}
