<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Framework\Uuid\Uuid;

class LabelProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'eclm_label';
    }

    public function getPayload(): array
    {
        $event1 = 'defdbac525174bb89330e337ea5139ac';
        $event2 = '39a9e62c75e84a80877931b11192d21b';

        return [
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 1',
                'active' => true,
                'mediaId' => '210e722ff9194a078b31f0e73e5f209a',
                'eventId' => $event1
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 2',
                'active' => true,
                'mediaId' => '14aecff9df5c42d5b5d974a3a64fefa3',
                'eventId' => $event1
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 3',
                'active' => true,
                'mediaId' => 'e0b64520d4864cd1adaa08340eac71d4',
                'eventId' => $event1
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 4',
                'active' => true,
                'mediaId' => '0e75e6e8570943e28d3b0a601161c221',
                'eventId' => $event2
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 5',
                'active' => true,
                'mediaId' => 'ff57ee0fa89f4a5492e6e55d1dd44614',
                'eventId' => $event2
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'Label 6',
                'active' => true,
                'mediaId' => '23842364084644a788c01e459a40daa4',
                'eventId' => $event2
            ]
        ];
    }
}
