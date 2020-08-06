<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Storefront\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
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


    public function __construct(
        NavigationPageLoader $navigationPageLoader
    ) {
        $this->navigationPageLoader = $navigationPageLoader;
    }

    /**
     * @Route("/label-me", name="eclm.labelme", options={"seo"="true"}, methods={"GET"})
     */
    public function labelMe(Request $request, SalesChannelContext $context): ?Response
    {

        $page = $this->navigationPageLoader->load($request, $context);

        return $this->renderStorefront(
            '@EventCandyLabelMe/storefront/page/eclm.html.twig',
            [
                'page' => $page,
            ]
        );
    }

}
