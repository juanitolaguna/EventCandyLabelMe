<?php


namespace EventCandy\LabelMe\Commands;


use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Doctrine\DBAL\Connection;

class CreateDemoData extends Command
{
    protected static $defaultName = 'eclm-commands:uuids';

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaFolderRepository;


    /**
     * @var Connection
     */
    private $connection;


    /**
     * CreateDemoData constructor.
     * @param Connection $connection
     * @param EntityRepositoryInterface $mediaFolderRepository
     */
    public function __construct(Connection $connection, EntityRepositoryInterface $mediaFolderRepository)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->mediaFolderRepository = $mediaFolderRepository;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generates random Uuid\'s')
            ->addOption(
                'iterations',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Number of iterations',
                '5'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'Event Candy'));
        /** @var MediaFolderEntity $result */
        $result = $this->mediaFolderRepository->search($criteria, Context::createDefaultContext())->first();

        if ($result !== null) {
            $output->writeln("Folder Name");
            $output->writeln($result->getName());
            $output->writeln("Folder ID");
            $output->writeln($result->getId());
        }

    }

    protected function executeX(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Generate Uuid's");

        for ($i = 0; $i <= $input->getOption('iterations'); $i++) {
            $output->writeln(Uuid::randomHex());
        }

        $thumbnails = $this->connection->fetchAll('
                select LOWER(HEX(media_thumbnail_size.id)) AS id from media_thumbnail_size
                where media_thumbnail_size.width in (400, 800, 1920);');

        $id = Uuid::randomHex();

        $output->writeln($id);
        $output->writeln(print_r($thumbnails));

        try {
            $this->mediaFolderRepository->upsert([
                [
                    'id' => $id,
                    'name' => 'Event Candy',
                    'useParentConfiguration' => false,
                    'configuration' => [
                        'id' => $id,
                        'createThumbnails' => true,
                        'keepAspectRation' => true,
                        'thumbnailQuality' => 80,
                        'mediaThumbnailSizes' => [
                            ['id' => $thumbnails[0]['id']],
                            ['id' => $thumbnails[1]['id']],
                            ['id' => $thumbnails[2]['id']]
                        ]
                    ]
                ]
            ], Context::createDefaultContext());
        } catch (ExceptionInterface $e) {
            $output->writeln($e->getMessage());
        }

    }


}
