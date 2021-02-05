<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1612532286AddAdditionalColumnsEventCandyPackage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1612532286;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            ALTER TABLE `eclm_event`
            ADD COLUMN `alternative_name` VARCHAR(255) NULL AFTER `name`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_candy`
            ADD COLUMN `product_data` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL AFTER `name`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_package`
            ADD COLUMN `product_data` MEDIUMTEXT COLLATE utf8mb4_unicode_ci NULL AFTER `name`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
