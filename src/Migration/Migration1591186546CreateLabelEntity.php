<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1591186546CreateLabelEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1591186484;
    }

    public function update(Connection $connection): void
    {

//        file_put_contents('log.txt', 'Create Label Entity', FILE_APPEND );

        $connection->exec('CREATE TABLE IF NOT EXISTS `eclm_label` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `active` TINYINT(1) NULL DEFAULT \'0\',
            `media_id` BINARY(16) NULL,
            `event_id` BINARY(16) NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`),
            KEY `fk.eclm_label.event_id` (`event_id`),
            KEY `fk.eclm_label.media_id` (`media_id`),
            CONSTRAINT `fk.eclm_label.event_id` FOREIGN KEY (`event_id`) REFERENCES `eclm_event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.eclm_label.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
