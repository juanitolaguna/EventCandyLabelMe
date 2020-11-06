<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1604615126AddGrammsColToCandyPackage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1604615126;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            ALTER TABLE `eclm_candy_package`
            ADD COLUMN `gramm` INT(11) NOT NULL DEFAULT 0 AFTER `media_id`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
