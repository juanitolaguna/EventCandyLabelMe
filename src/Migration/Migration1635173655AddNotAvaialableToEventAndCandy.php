<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1635173655AddNotAvaialableToEventAndCandy extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1635173655;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
            ALTER TABLE `eclm_event`
            ADD COLUMN `not_available` tinyint(1) NOT NULL DEFAULT '0' after `active`
        ");

        $connection->executeStatement("
            ALTER TABLE `eclm_candy`
            ADD COLUMN `not_available` tinyint(1) NOT NULL DEFAULT '0' after `active`
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
