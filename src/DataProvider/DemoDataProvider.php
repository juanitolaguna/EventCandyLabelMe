<?php

namespace EventCandy\LabelMe\DataProvider;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Framework\Context;

abstract class DemoDataProvider
{
    /**
     * @var FileSaver
     */
    protected $fileSaver;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection, FileSaver $fileSaver)
    {
        $this->fileSaver = $fileSaver;
        $this->connection = $connection;
    }

    public function getPluginMediaFolderId(string $folderName)
    {
        $folder = '%' . $folderName . '%';
        $result = $this->connection->fetchColumn("
            select lower(hex(media_folder.id))
            from media_folder where media_folder.name like :name", ['name' => $folder]);

        if (!$result) {
            throw new \RuntimeException('No Folder with name "' . $folderName . '" found, please make sure that basic data is available by running the migrations.');
        }
        return (string) $result;
    }

    abstract public function getAction(): string;

    abstract public function getEntity(): string;

    abstract public function getPayload(): array;

    public function finalize(Context $context): void
    {
    }

    public function prepare(Context $context): void
    {
    }
}
