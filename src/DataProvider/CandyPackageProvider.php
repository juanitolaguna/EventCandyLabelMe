<?php


namespace EventCandy\LabelMe\DataProvider;


use Shopware\Core\Framework\Uuid\Uuid;

class CandyPackageProvider extends DemoDataProvider
{

    public function getAction(): string
    {
        return 'upsert';
    }

    public function getEntity(): string
    {
        return 'eclm_candy_package';
    }

    public function getPayload(): array
    {
        $payload = [];

        $result = $this->connection->fetchAll("
            select lower(hex(eclm_candy.id)) as id
            from eclm_candy");

        $result2 = $this->connection->fetchAll("
            select lower(hex(eclm_package.id)) as id
            from eclm_package");

        $noimage = $this->connection->fetchColumn("
         select lower(hex(media.id))
            from media where media.file_name like '%noimage%'");

        foreach ($result as $candy) {
            foreach ($result2 as $package) {
                $c_p = [
                    'id' => Uuid::randomHex(),
                    'candyId' => $candy['id'],
                    'packageId' => $package['id'],
                    'mediaId' => $noimage
                ];

                array_push($payload, $c_p);
            }
        }

        return $payload;
    }
}
