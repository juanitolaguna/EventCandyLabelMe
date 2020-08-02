<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Framework\Uuid\Uuid;

class CandyProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'eclm_candy';
    }

    public function getPayload(): array
    {
        return [
            [
                'id' => Uuid::randomHex(),
                'name' => 'Candy_2',
                'active' => true,
                'mediaId' => 'b9c207ca62da47a1ac9019c475daaf02'
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Candy_1',
                'active' => true,
                'mediaId' => 'c7ce0e396809439c8997ae68992924aa'
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Candy_3',
                'active' => true,
                'mediaId' => 'c614ef6e428a4d7ab861ff8a2b98d2f3'
            ]
        ];
    }
}
