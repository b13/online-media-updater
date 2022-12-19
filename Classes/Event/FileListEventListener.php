<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Event;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\Event\ProcessFileListActionsEvent;

final class FileListEventListener
{
    protected IconFactory $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    public function __invoke(ProcessFileListActionsEvent $event): void
    {
        $actionItems = $event->getActionItems();

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@online-media-updater/Updater.js');
        $pageRenderer->addInlineLanguageLabelFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        $fileOrFolderObject = $event->getResource();
        if ($fileOrFolderObject instanceof File) {
            $fileProperties = $fileOrFolderObject->getProperties();
            $extension = $fileProperties['extension'] ?? '';

            $registeredHelpers = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] ?? [];
            if (!array_key_exists($extension, $registeredHelpers)) {
                return;
            }

            $actionItems['tiktok'] = '<a href="#" class="btn btn-default t3js-filelist-update-metadata'
                . '" data-filename="' . htmlspecialchars($fileOrFolderObject->getName())
                . '" data-file-uid="' . $fileProperties['uid']
                . '" title="' . $this->getLanguageService()->getLL('online_media_updater.update') . '">'
                . $this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL)->render() . '</a>';
        }

        $event->setActionItems($actionItems);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        $languageService = $GLOBALS['LANG'];
        $languageService->includeLLFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        return $languageService;
    }
}
