<?php declare(strict_types=1);


namespace EventCandy\LabelMe;

use EventCandy\LabelMe\DataProvider\DemoDataProvider;
use Shopware\Core\Framework\Api\Controller\SyncController;
use Shopware\Core\Framework\Context;
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;


class DemoDataService
{

    /**
     * @var SyncController
     */
    private $sync;

    /**
     * @var DemoDataProvider[]
     */
    private $demoDataProvider;

    public function __construct(SyncController $sync, iterable $demoDataProvider)
    {
        $this->sync = $sync;
        $this->demoDataProvider = $demoDataProvider;
    }


    public function generate(Context $context): void
    {

//        file_put_contents('log.txt', 'generate', FILE_APPEND);
        /** @var DemoDataProvider $dataProvider */
        foreach ($this->demoDataProvider as $dataProvider) {

            $dataProvider->prepare($context);

//            file_put_contents('log.txt', "Action\n" . $dataProvider->getAction(), FILE_APPEND);

            $payload = [
                [
                    'action' => $dataProvider->getAction(),
                    'entity' => $dataProvider->getEntity(),
                    'payload' => $dataProvider->getPayload()
                ]
            ];

            $response = $this->sync->sync(new Request([], [], [], [], [], [], json_encode($payload)), $context, PlatformRequest::API_VERSION);
            $result = json_decode($response->getContent(), true);

            if (isset($result['errors']) && count($result['errors']) > 0) {
                throw new \RuntimeException(sprintf('Error importing "%s": %s', $dataProvider->getEntity(), print_r($result['errors'], true)));
            }

            $dataProvider->finalize($context);
        }
    }
}
