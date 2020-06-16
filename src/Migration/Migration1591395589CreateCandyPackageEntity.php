<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1591395589CreateCandyPackageEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1591395589;
    }

    public function update(Connection $connection): void
    {
        $connection->exec('CREATE TABLE `eclm_candy_package` (
            `id` BINARY(16) NOT NULL,
            `candy_id` BINARY(16) NOT NULL,
            `package_id` BINARY(16) NOT NULL,
            `media_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`),
            KEY `fk.eclm_candy_package.candy_id` (`candy_id`),
            KEY `fk.eclm_candy_package.package_id` (`package_id`),
            KEY `fk.eclm_candy_package.media_id` (`media_id`),
            CONSTRAINT `fk.eclm_candy_package.candy_id` FOREIGN KEY (`candy_id`) REFERENCES `eclm_candy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.eclm_candy_package.package_id` FOREIGN KEY (`package_id`) REFERENCES `eclm_package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.eclm_candy_package.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
