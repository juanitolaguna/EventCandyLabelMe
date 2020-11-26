<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1606224157ExtendPackageEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1606224157;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            ALTER TABLE `eclm_package`
            ADD COLUMN `css_size` INT(11) NULL AFTER `active`
        ');

        $connection->executeUpdate('
            ALTER TABLE `eclm_package`
            ADD COLUMN `package_type` VARCHAR(255) NULL AFTER `css_size`
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
