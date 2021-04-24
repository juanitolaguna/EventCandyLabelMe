<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1619265445AddDefaultToMillis extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1619265445;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            ALTER TABLE `eclm_package`
            ALTER `milliliters` SET DEFAULT 0;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
