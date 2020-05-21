<?php declare( strict_types=1 );

namespace EventCandyLabelMe\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1589228524 extends MigrationStep {
    public function getCreationTimestamp(): int {
        return 1589228524;
    }

    public function update( Connection $connection ): void {

        $connection->exec( 'CREATE TABLE IF NOT EXISTS `eclm_candy` (
            `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `eur_pro_ml` INT(11) NOT NULL,
            `media_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`),
            KEY `fk.candy.media_id` (`media_id`),
            CONSTRAINT `fk.candy.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;' );

    }

    public function updateDestructive( Connection $connection ): void {
        // implement update destructive
    }
}
