<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Framework\Uuid\Uuid;

class PackageProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'eclm_package';
    }

    public function getPayload(): array
    {
        return [
            [
                'id' => Uuid::randomHex(),
                'name' => 'Package 3',
                'active' => true,
                'milliliters' => 100,
                'mediaId' => '8f21cd6ccb4947989971952e572a1b6b'
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Package 1',
                'active' => true,
                'milliliters' => 200,
                'mediaId' => '9548f3a1da53447492ddd1a5595e2069'
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Package 2',
                'active' => true,
                'milliliters' => 300,
                'mediaId' => '345520539f7a479285491d21decc45d4'
            ]
        ];
    }
}
