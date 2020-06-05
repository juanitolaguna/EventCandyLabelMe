<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1591395506CreatePackageEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1591395506;
    }

    public function update(Connection $connection): void
    {
        $connection->exec('CREATE TABLE `eclm_package` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NULL DEFAULT \'0\',
            `media_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`),
            KEY `fk.eclm_package.media_id` (`media_id`),
            CONSTRAINT `fk.eclm_package.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
