<?php declare(strict_types=1);

namespace EventCandy\LabelMe\Storefront\Controller;

use Composer\Package\Package;
use EventCandy\LabelMe\Core\Content\Candy\CandyEntity;
use EventCandy\LabelMe\Core\Content\CandyPackage\CandyPackageEntity;
use EventCandy\LabelMe\Core\Content\Event\EventEntity;
use EventCandy\LabelMe\Core\Content\Label\LabelEntity;
use EventCandy\LabelMe\Core\Content\Package\PackageEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    private $packageRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $candyPackageRepository;

    public function __construct(
        EntityRepositoryInterface $eventRepository,
        EntityRepositoryInterface $labelRepository,
        EntityRepositoryInterface $packageRepository,
        EntityRepositoryInterface $candyPackageRepository
    )
    {
        $this->eventRepository = $eventRepository;
        $this->labelRepository = $labelRepository;
        $this->packageRepository = $packageRepository;
        $this->candyPackageRepository = $candyPackageRepository;
    }


    /**
     * @Route("/store-api/v{version}/eclm/get-events", name="api.action.eclm.get-events", methods={"GET"})
     */
    public function getEvents(Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addAssociation('media');

        $entities = $this->eventRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $filter = function(EventEntity $event) {
            return [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'thumbnails' => $event->getMedia()->getThumbnails()
            ];
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
        $criteria->addFilter(new EqualsFilter('eventId', $id));
        $criteria->addAssociation('media');


        $entities =  $this->labelRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $filter = function(LabelEntity $label) {
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
     * @Route("/store-api/v{version}/eclm/get-packages", name="api.action.eclm.get-packages", methods={"GET"})
     */
    public function getPackages(Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('media');

        $entities = $this->packageRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $filter = function(PackageEntity $event) {
            return [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'thumbnails' => $event->getMedia()->getThumbnails()
            ];
        };

        $mapped = $entities->fmap($filter);
        return new JsonResponse($mapped);
    }

    /**
     * @Route("/store-api/v{version}/eclm/get-candies/{id}", name="api.action.eclm.get-candies", methods={"GET"})
     * @param string $id packageId
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function getCandies(string $id, Request $request, Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('packageId', $id));
        $criteria->addAssociation('candy');
        $criteria->addAssociation('media');


        $entities =  $this->candyPackageRepository->search(
            $criteria,
            Context::createDefaultContext()
        );

        $filter = function(CandyPackageEntity $cp) {
            return [
                'cp_id' => $cp->getId(),
                'id' => $cp->getCandy()->getId(),
                'name' => $cp->getCandy()->getName(),
                'thumbnails' => $cp->getMedia()->getThumbnails()
            ];
        };

        $mapped = $entities->fmap($filter);

        return new JsonResponse($mapped);
    }


}
