<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Framework\Uuid\Uuid;

class EventProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'eclm_event';
    }

    public function getPayload(): array
    {
        return [
            [
                'id' => 'defdbac525174bb89330e337ea5139ac',
                'name' => 'Event 2',
                'active' => true,
                'mediaId' => '9c65e418f2234c5ca4a23c96db811e36'
            ],
            [
                'id' => '39a9e62c75e84a80877931b11192d21b',
                'name' => 'Event 1',
                'active' => true,
                'mediaId' => 'e9c3f76217c44fbd90f71bec6631b14b'
            ]
        ];
    }
}
