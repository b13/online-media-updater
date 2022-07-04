<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class OnlineMediaController handles uploading online media
 * @internal This class is a specific Backend controller implementation and is not considered part of the Public TYPO3 API.
 */
class OnlineMediaUpdateController
{
    /**
     * AJAX endpoint for storing the URL as a sys_file record
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function updateAction(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new JsonResponse(['Please use a POST request'], 500);
        }

        $parsedBody = $request->getParsedBody();
        $identifier = $parsedBody['identifier'];
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
        $file = $resourceFactory->getFileObjectFromCombinedIdentifier($identifier);

        $onlineMediaViewHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)
            ->getOnlineMediaHelper($file);

        $videoId = $onlineMediaViewHelper->getOnlineMediaId($file);
        $temporaryFileName = $this->getTempFolderPath() . $file->getExtension() . '_' . md5($videoId) . '.jpg';
        @unlink($temporaryFileName);
        $previewPath = $onlineMediaViewHelper->getPreviewImage($file);

        $this->updatePreviewDimensions($file, $previewPath);
        $this->updateProcessedFiles($file);

        return new JsonResponse();
    }

    protected function getTempFolderPath(): string
    {
        $path = Environment::getPublicPath() . '/typo3temp/assets/online_media/';
        if (!is_dir($path)) {
            GeneralUtility::mkdir_deep($path);
        }
        return $path;
    }

    /**
     * Update processed files
     * @todo: regenerated processed files, currently only done on reload
     */
    protected function updateProcessedFiles(File $file): void
    {
        $processedFileRepository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        $processedFiles  = $processedFileRepository->findAllByOriginalFile($file);

        foreach ($processedFiles as $processedFile) {
            $processedFile->delete();
            // @todo: Update processed File
            //        $file->getMetaData()->offsetSet('width', (int)$width);
            //            $processedFileNew = $file->process(
            //                ProcessedFile::CONTEXT_IMAGEPREVIEW,
            //                [
            //                    'width' => 12,
            //                    'height' => 12,
            //                ]
            //            );
        }
    }

    protected function updatePreviewDimensions(File $file, string $path): void
    {
        [$width, $height] = getimagesize($path);
        $metadataRepository = GeneralUtility::makeInstance(MetaDataRepository::class);
        $metadataRepository->update($file->getUid(), [
            'width' => (int)$width,
            'height' => (int)$height,
        ]);
    }
}
