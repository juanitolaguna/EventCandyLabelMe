<?php


namespace EventCandy\LabelMe\Commands;


use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class CreateDemoData
 * @package EventCandy\LabelMe\Commands
 *
 * Create EntityFolders with Dummy Images:
 *  - bin/console eclm:utils --folders=5 --imagePrefix=candy
 *
 * Execute Tinker Code
 *  - bin/console eclm:utils --tinker=true
 */
class EclmCommands extends Command
{
    protected static $defaultName = 'eclm:utils';

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaFolderRepository;


    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dir = __DIR__ . '/../Resources/media/';


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
            ->setDescription('Plugin Utils & Automation')
            ->addOption(
                'folders',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Create uuid Folders and print uuids',
                '0')
            ->addOption(
                'imagePrefix',
                'im',
                InputOption::VALUE_OPTIONAL,
                'Create Image',
                '')
            ->addOption(
                'tinker',
                't',
                InputOption::VALUE_OPTIONAL,
                'Execute & Test Code',
                false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('folders')) {
            $this->createFolders($input, $output);
        }

        if ($input->getOption('tinker')) {
            $this->tinker($input, $output);
        }
    }


    private function createFolders(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('create folders..');

        for ($i = 1; $i <= $input->getOption('folders'); $i++) {
            $uuid = Uuid::randomHex();

            $output->writeln($uuid);

            if ($input->getOption('imagePrefix')) {
                $folderPath = $this->dir . $input->getOption('imagePrefix') . '/' . $uuid;
            } else {
                $folderPath = $this->dir . $uuid;
            }
            mkdir($folderPath, 0764, true);

            if ($input->getOption('imagePrefix')) {
                $this->createImage($input, $output, $folderPath, $i);
            }
        }

    }

    private function createImage(InputInterface $input, OutputInterface $output, string $path, int $i)
    {
        $image = imagecreate(200, 150);
        $bg = imagecolorallocate($image, 255, 255, 255);
        $textcolor = imagecolorallocate($image, 0, 0, 0);
        $text = $input->getOption('imagePrefix') . ' ' . $i;
        imagestring($image, 40, 10, 50, $text, $textcolor);
        $savePath = $path . '/' . $input->getOption('imagePrefix') . '_' . $i . '.png';
        imagepng($image, $savePath);
    }

    private function tinker(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Tinker...');
        $result = $this->connection->fetchColumn("
            select lower(hex(media_folder.id))
            from media_folder where media_folder.name like :name", ['name' => '%Event Candy%']);

        $output->writeln($result);
    }
}
