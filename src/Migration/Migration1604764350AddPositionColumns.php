<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1604764350AddPositionColumns extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1604764350;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            ALTER TABLE `eclm_event`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0 AFTER `media_id`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_label`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0 AFTER `media_id`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_candy`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0 AFTER `media_id`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_package`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0 AFTER `media_id`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
