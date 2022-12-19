<?php

declare(strict_types=1);

namespace B13\OnlineMediaUpdater\Command;

use B13\OnlineMediaUpdater\Domain\Repository\FileRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\VimeoHelper;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;
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
    }

    public function __construct(
        protected FileRepository     $fileRepository,
        protected MetaDataRepository $metadataRepository,
        protected ResourceFactory    $resourceFactory
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = (int)($input->getOption('limit') ?? 10);

        $extensions = ['youtube', 'vimeo'];
        foreach ($extensions as $extension) {

            // TODO Factory?
            $class = match ($extension) {
                'youtube' => YouTubeHelper::class,
                'vimeo' => VimeoHelper::class,
                default => '',
            };

            if ($class === '') {
                return Command::FAILURE;
            }

            $io->section($extension);
            $table = new Table($output);
            $rows = [];

            $helper = GeneralUtility::makeInstance($class, $extension);
            $videos = $this->fileRepository->getVideosByFileExtension($extension, $limit);
            foreach ($videos as $video) {
                $file = $this->resourceFactory->getFileObject($video['uid']);
                $metaData = $helper->getMetaData($file);
                if (!empty($metaData)) {
                    //TODO Imagehandling?
                    $newData = [
                        'width' => (int)$metaData['width'],
                        'height' => (int)$metaData['height']
                    ];
                    if (isset($metaData['title'])) {
                        $newData['title'] = $metaData['title'];
                    }
                    if (isset($metaData['author'])) {
                        $newData['author'] = $metaData['author'];
                    }

                    $this->metadataRepository->update($file->getUid(), $newData);
                    $rows[] = [$file->getUid(), $file->getProperty('title'), $file->getPublicUrl()];
                }
            }

            $table
                ->setHeaders(['UID', 'Title', 'Public URL'])
                ->setRows($rows);
            $table->render();
            $io->newLine(1);
        }

        return Command::SUCCESS;
    }
}
