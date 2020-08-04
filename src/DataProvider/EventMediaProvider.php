<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;

class EventMediaProvider extends DemoDataProvider
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
                'id' => 'e9c3f76217c44fbd90f71bec6631b14b',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '9c65e418f2234c5ca4a23c96db811e36',
                'mediaFolderId' => $folderId
            ]
        ];
    }

    public function finalize(Context $context): void
    {
        foreach (glob(__DIR__ . '/../Resources/media/event/*/*.png') as $file) {
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
