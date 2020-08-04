<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\Context;

class LabelMediaProvider extends DemoDataProvider
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
                'id' => '210e722ff9194a078b31f0e73e5f209a',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '14aecff9df5c42d5b5d974a3a64fefa3',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => 'e0b64520d4864cd1adaa08340eac71d4',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '0e75e6e8570943e28d3b0a601161c221',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => 'ff57ee0fa89f4a5492e6e55d1dd44614',
                'mediaFolderId' => $folderId
            ],
            [
                'id' => '23842364084644a788c01e459a40daa4',
                'mediaFolderId' => $folderId
            ],

        ];
    }


    public function finalize(Context $context): void
    {
        foreach (glob(__DIR__ . '/../Resources/media/label/*/*.png') as $file) {
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
