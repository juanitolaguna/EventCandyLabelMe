<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;

class PackageMediaProvider extends DemoDataProvider
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
                'id' => '9548f3a1da53447492ddd1a5595e2069',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '345520539f7a479285491d21decc45d4',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '8f21cd6ccb4947989971952e572a1b6b',
                'mediaFolderId' => $folderId
            ]
        ];
    }

    public function finalize(Context $context): void
    {
        foreach (glob(__DIR__ . '/../Resources/media/package/*/*.png') as $file) {
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
