<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Command;

use B13\OnlineMediaUpdater\Domain\Repository\FileRepository;
use B13\OnlineMediaUpdater\Event\ModifyMetaDataEvent;
use B13\OnlineMediaUpdater\Event\ModifyOnlineMediaHelperEvent;
use B13\OnlineMediaUpdater\Factory\OnlineMediaHelperFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateMetadataCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Updates online media metadata');
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_OPTIONAL,
            'Defines the number of videos to be checked',
            10
        );
        $this->addOption(
            'fileExtensions',
            null,
            InputOption::VALUE_OPTIONAL,
            'Defines the file extension(s). Multiple extensions are separated with a comma',
            'youtube,vimeo'
        );
    }

    public function __construct(
        protected FileRepository           $fileRepository,
        protected MetaDataRepository       $metadataRepository,
        protected ResourceFactory          $resourceFactory,
        protected EventDispatcherInterface $eventDispatcher,
        protected OnlineMediaHelperFactory $onlineMediaHelperFactory,
        protected ProcessedFileRepository  $processedFileRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)($input->getOption('limit'));
        $fileExtensions = GeneralUtility::trimExplode(',', $input->getOption('fileExtensions'));

        foreach ($fileExtensions as $fileExtension) {
            $io->section($fileExtension);

            $onlineMediaHelper = $this->onlineMediaHelperFactory->createOnlineMediaHelper($fileExtension);
            $modifyOnlineMediaHelperEvent = $this->eventDispatcher->dispatch(
                new ModifyOnlineMediaHelperEvent($onlineMediaHelper, $fileExtension)
            );
            $onlineMediaHelper = $modifyOnlineMediaHelperEvent->getOnlineMediaHelper();

            if ($onlineMediaHelper === null) {
                $io->warning('There is no OnlineMediaHelper registered for the file extension ' . $fileExtension);
                continue;
            }

            $videos = $this->fileRepository->getVideosByFileExtension($fileExtension, $limit);
            if ($videos) {
                $table = new Table($output);
                $rows = [];

                foreach ($videos as $video) {
                    $file = $this->resourceFactory->getFileObject($video['uid']);
                    $metaData = $onlineMediaHelper->getMetaData($file);
                    if (!empty($metaData)) {
                        $this->metadataRepository->update(
                            $file->getUid(),
                            $this->handleMetaData($metaData, $file)
                        );
                        $this->handlePreviewImage($onlineMediaHelper, $file);

                        $rows[] = [$file->getUid(), $file->getProperty('title'), $file->getPublicUrl()];
                    }
                }

                $table
                    ->setHeaders(['UID', 'Title', 'Public URL'])
                    ->setRows($rows);
                $table->render();
                $io->newLine(1);
            }
        }

        return Command::SUCCESS;
    }

    protected function handleMetaData(array $metaData, File $file): array
    {
        $newData = [
            'width' => (int)$metaData['width'],
            'height' => (int)$metaData['height']
        ];
        if (isset($metaData['title'])) $newData['title'] = $metaData['title'];
        if (isset($metaData['author'])) $newData['author'] = $metaData['author'];

        $modifyMetaDataEvent = $this->eventDispatcher->dispatch(
            new ModifyMetaDataEvent($file, $newData)
        );
        return $modifyMetaDataEvent->getMetaData();
    }

    protected function getTempFolderPath(): string
    {
        $path = Environment::getPublicPath() . '/typo3temp/assets/online_media/';
        if (!is_dir($path)) {
            GeneralUtility::mkdir_deep($path);
        }
        return $path;
    }

    protected function handlePreviewImage(AbstractOnlineMediaHelper $onlineMediaHelper, File $file): void
    {
        $processedFiles = $this->processedFileRepository->findAllByOriginalFile($file);
        foreach ($processedFiles as $processedFile) {
            $processedFile->delete();
        }

        $videoId = $onlineMediaHelper->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . $file->getExtension() . '_' . md5($videoId) . '.jpg';
        @unlink($temporaryFileName);
        $onlineMediaHelper->getPreviewImage($file);
    }
}
