<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Storefront\Controller;

use ErrorException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Shopware\Storefront\Page\MetaInformation;
use Shopware\Storefront\Page\Navigation\NavigationPageLoader;
use Shopware\Storefront\Pagelet\Menu\Offcanvas\MenuOffcanvasPageletLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @RouteScope(scopes={"storefront"})
 */
class LabelMeController extends StorefrontController
{

    /**
     * @var NavigationPageLoader
     */
    private $navigationPageLoader;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;


    public function __construct(
        NavigationPageLoader $navigationPageLoader,
        SystemConfigService $systemConfigService
    ) {
        $this->navigationPageLoader = $navigationPageLoader;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/label-me", name="eclm.labelme", options={"seo"="true"}, methods={"GET"})
     */
    public function labelMe(Request $request, SalesChannelContext $context): ?Response
    {

        $page = $this->navigationPageLoader->load($request, $context);
        $config = $this->systemConfigService->get('EventCandyLabelMe.config');

        /** @var MetaInformation $metaInformation */
        $metaInformation = $page->getMetaInformation();
        $metaInformation->setMetaTitle($config['lmSEOMetaTitle']);
        $metaInformation->setMetaDescription($config['lmSEOMetaDescription']);

        $days = $config['lmSeoRevisitAfter'];
        if ($days == 0 || $days == null) {
            $days = 15;
        }
        $metaInformation->setRevisit((string) $days . ' days');
        $metaInformation->setMetaKeywords($config['lmSeoKeywords']);
        $page->setMetaInformation( $metaInformation);


        return $this->renderStorefront(
            '@EventCandyLabelMe/storefront/page/eclm.html.twig',
            [
                'page' => $page,
            ]
        );
    }

}
