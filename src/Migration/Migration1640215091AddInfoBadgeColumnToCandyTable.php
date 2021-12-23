<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1640215091AddInfoBadgeColumnToCandyTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1640215091;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `eclm_candy`
            ADD COLUMN `info_badge` VARCHAR(255) NULL AFTER `name`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
