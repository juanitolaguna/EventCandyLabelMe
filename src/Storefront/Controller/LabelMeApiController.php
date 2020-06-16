<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"store-api"})
 */
class LabelMeApiController
{
    /**
         * @Route("/store-api/v{version}/eclm/get-events", name="api.action.eclm.get-events", methods={"GET"})
     */
    public function myFirstApi(Request $request, Context $context): JsonResponse
    {
        return new JsonResponse([
            'name' => 'Pavel Snatenkov',
            'address' => 'Kettelerweg 7'
        ]);
    }
}
