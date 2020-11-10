<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Storefront\Controller;

use ErrorException;
use EventCandy\LabelMe\Core\Content\CandyPackage\CandyPackageEntity;
use EventCandy\LabelMe\Core\Content\Event\EventEntity;
use EventCandy\LabelMe\Core\Content\Label\LabelEntity;
use EventCandy\Sets\Storefront\Page\Product\Subscriber\ProductListingSubscriber;
use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartPersister;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\Currency\CurrencyEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Framework\Twig\Extension\SwSanitizeTwigFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"store-api"})
 */
class LabelMeApiController extends AbstractController
{

    /**
     * @var EntityRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $candyRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $candyPackageRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var CartPersister
     */
    private $cartPersister;

    /**
     * Contains available Stock Calculation method.
     * @var ProductListingSubscriber
     */
    private $productListingSubscriber;


    public function __construct(
        EntityRepositoryInterface $eventRepository,
        EntityRepositoryInterface $labelRepository,
        EntityRepositoryInterface $candyRepository,
        EntityRepositoryInterface $candyPackageRepository,
        EntityRepositoryInterface $currencyRepository,
        SystemConfigService $systemConfigService,
        CartService $cartService,
        CartPersister $cartPersister,
        ProductListingSubscriber $productListingSubscriber
    )
    {
        $this->eventRepository = $eventRepository;
        $this->labelRepository = $labelRepository;
        $this->candyRepository = $candyRepository;
        $this->candyPackageRepository = $candyPackageRepository;
        $this->currencyRepository = $currencyRepository;
        $this->systemConfigService = $systemConfigService;
        $this->cartService = $cartService;
        $this->cartPersister = $cartPersister;
        $this->productListingSubscriber = $productListingSubscriber;
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-events", name="api.action.eclm.get-events", methods={"GET"})
     */
    public function getEvents(Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria
            ->addFilter(new EqualsFilter('active', true))
            ->addAssociation('media')
            ->addSorting(new FieldSorting('position', FieldSorting::DESCENDING));

        $entities = $this->eventRepository->search(
            $criteria,
            $context
        );


        $filter = function (EventEntity $event) {
            $media = $event->getMedia();
            if (($media !== null) && ($media->getThumbnails() !== null)) {
                return [
                    'id' => $event->getId(),
                    'name' => $event->getName(),
                    'thumbnails' => $event->getMedia()->getThumbnails()
                ];
            }


        };

        $mapped = $entities->fmap($filter);
        return new JsonResponse($mapped);
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-labels/{id}", name="api.action.eclm.get-labels", methods={"GET"})
     */
    public function getLabels(string $id, Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria
            ->addFilter(new EqualsFilter('eventId', $id))
            ->addFilter(new EqualsFilter('active', true))
            ->addAssociation('media')
            ->addSorting(new FieldSorting('position', FieldSorting::DESCENDING));


        $entities = $this->labelRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $filter = function (LabelEntity $label) {
            return [
                'id' => $label->getId(),
                'name' => $label->getName(),
                'thumbnails' => $label->getMedia()->getThumbnails()
            ];
        };

        $mapped = $entities->fmap($filter);

        return new JsonResponse($mapped);
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-candies", name="api.action.eclm.get-candies", methods={"GET"})
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function getCandies(Request $request, Context $context): JsonResponse
    {


        $criteria = new Criteria();
        $criteria
            ->addAssociation('candy')
            ->addAssociation('candy.media')
            ->addAssociation('package')
            ->addAssociation('product')
            ->addSorting(new FieldSorting('candy.position', FieldSorting::DESCENDING));


        $entities = $this->candyPackageRepository->search(
            $criteria,
            $context
        );


        $filter = function (CandyPackageEntity $cp) use ($context) {

            $candyActive = false;
            $packageAvailable = false;
            $stock = 0;

            if ($cp->getCandy() !== null) {
                $candyActive = $cp->getCandy()->isActive();
            }


            if ($cp->getPackage() !== null) {
                $packageAvailable = $cp->getPackage()->isActive();
            }


            if (($cp->getProduct() !== null) && ($cp->getProduct()->getAvailableStock() !== null)) {

                $keyIsTrue = array_key_exists('ec_is_set', $cp->getProduct()->getCustomFields())
                    && $cp->getProduct()->getCustomFields()['ec_is_set'];

                if ($keyIsTrue) {
                    $stock = $this->productListingSubscriber->getAvailableStock($cp->getProduct()->getId(), $context);
                } else {
                    //turn off normal products
                    //$stock = $cp->getProduct()->getAvailableStock();
                    $stock = 0;

                }
            }


            if ($candyActive && $packageAvailable && $stock > 0) {
                return [
                    'id' => $cp->getCandy()->getId(),
                    'name' => $cp->getCandy()->getName(),
                    'thumbnails' => $cp->getCandy()->getMedia()->getThumbnails(),
                    'availableStock' => $stock
                ];
            }

        };

        $mapped = $entities->fmap($filter);

        $deduplicated = [];
        $candies = [];
        foreach ($mapped as $key => $candy) {
            if (!in_array($candy['name'], $candies)) {
                $candies[] = $candy['name'];
                $deduplicated[$key] = $candy;
            }
        }

        if (empty($deduplicated)) {
            return new JsonResponse(false);
        }
        return new JsonResponse($deduplicated);
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-packages/{id}", name="api.action.eclm.get-packages", methods={"GET"})
     */
    public function getPackages(string $id, Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria
            ->addFilter(new EqualsFilter('candyId', $id))
            ->addAssociation('package')
            ->addAssociation('media')
            ->addAssociation('product')
            ->addSorting(new FieldSorting('package.position', FieldSorting::DESCENDING));

        $entities = $this->candyPackageRepository->search(
            $criteria,
            $context
        );

        /** @var CurrencyEntity $currency */
        $currency = $this->currencyRepository
            ->search(new Criteria([$context->getCurrencyId()]), $context)
            ->first();
        $currencySymbol = $currency->getSymbol();

        $filter = function (CandyPackageEntity $cp) use ($context, $currencySymbol) {
            $packageActive = $cp->getPackage()->isActive();
            $productAvailable = 0;
            if (($cp->getProduct() !== null) && ($cp->getProduct()->getAvailableStock() !== null)) {
                $keyIsTrue = array_key_exists('ec_is_set', $cp->getProduct()->getCustomFields())
                    && $cp->getProduct()->getCustomFields()['ec_is_set'];
                if ($keyIsTrue) {
                    $productAvailable = $this->productListingSubscriber->getAvailableStock($cp->getProduct()->getId(), $context);
                } else {
                    //turn off normal products
                    $productAvailable = $cp->getProduct()->getAvailableStock();
                    $productAvailable = 0;
                }
            }

            if ($packageActive && $productAvailable > 0) {

                return [
                    'cp_id' => $cp->getId(),
                    'id' => $cp->getPackage()->getId(),
                    'name' => $cp->getPackage()->getName(),
                    'gramm' => $cp->getGramm(),
                    'thumbnails' => $cp->getMedia()->getThumbnails(),
                    'availableStock' => $productAvailable,
                    'purchaseSteps' => $cp->getProduct()->getPurchaseSteps(),
                    'minimalQuantity' => $cp->getProduct()->getMinPurchase(),
                    'maximalQuantity' => $cp->getProduct()->getMaxPurchase(),
                    'product' => [
                        'id' => $cp->getProduct()->getId(),
                        'name' => $cp->getProduct()->getName(),
                        'price' => $cp->getProduct()->getCurrencyPrice($context->getCurrencyId()),
                        'currency' => $currencySymbol
                    ]
                ];
            }
        };

        $mapped = $entities->fmap($filter);
        return new JsonResponse($mapped);
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-config", name="api.action.eclm.get-config", methods={"GET"})
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function getPluginConfig(Request $request, Context $context): JsonResponse
    {
//        $name = $this->container->get('EventCandy\LabelMe\EventCandyLabelMe')->getName();
        $config = $this->systemConfigService->get('EventCandyLabelMe.config');
        return new JsonResponse($config);
    }

    /**
     * @Route("/store-api/v{version}/eclm/add-line-item", name="api.action.add-line-item", methods={"POST"}, defaults={"XmlHttpRequest": true})
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function addLineItems(Cart $cart, RequestDataBag $requestDataBag, Request $request, SalesChannelContext $salesChannelContext): Response
    {

        $lineItemData = $requestDataBag->all();


        if (!$lineItemData['eclm_package']) {
            throw new MissingRequestParameterException('Bad Payload');
        }



        try {
            //throw new ErrorException('bug');
            $id = $lineItemData['eclm_package']['product']['id'];

            $lineItem = new LineItem(
                $id,
                'event-candy-label-me',
                $id,
                intval($lineItemData['selectedQuantity'])
            );


            $lineItem->setPayload($lineItemData);
            $lineItem->setStackable(true);

            $this->cartService->add($cart, $lineItem, $salesChannelContext);
            $this->cartPersister->save($cart, $salesChannelContext);


        } catch (Exception $exception) {
            return new JsonResponse($exception->getMessage() );
        }

        return $this->redirectToRoute('frontend.cart.offcanvas');
    }

    /**
     * @Route("/store-api/v{version}/eclm/get-available-stock/{id}", name="api.action.eclm.get-available-stock", methods={"GET"})
     * @param string $id
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function getStock(string $id, Request $request, Context $context): JsonResponse
    {
        $availableStock = $this->productListingSubscriber->getAvailableStock($id, $context);
        return new JsonResponse($availableStock);
    }


}
