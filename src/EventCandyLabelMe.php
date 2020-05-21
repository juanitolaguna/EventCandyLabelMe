<?php declare( strict_types=1 );

namespace EventCandyLabelMe;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class EventCandyLabelMe extends Plugin {
    public function uninstall( UninstallContext $uninstallContext ): void {
        if ( $uninstallContext->keepUserData() ) {
            return;
        }

        $this->container->get( Connection::class )->exec( 'DROP TABLE IF EXISTS eclm_candy' );
    }
}
