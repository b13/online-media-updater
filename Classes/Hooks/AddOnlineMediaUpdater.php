<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Hooks;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\FileListEditIconHookInterface;

class AddOnlineMediaUpdater implements FileListEditIconHookInterface
{

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    public function manipulateEditIcons(&$cells, &$parentObject): void
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/OnlineMediaUpdater/Backend/Updater');
        $pageRenderer->addInlineLanguageLabelFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        $fileOrFolderObject = $cells['__fileOrFolderObject'];
        if ($fileOrFolderObject instanceof File) {
            $fileProperties = $fileOrFolderObject->getProperties();
            $extension = $fileProperties['extension'] ?? '';

            if (!in_array($extension, ['youtube', 'vimeo'])) {
                return;
            }

            $cells['updateOnlineMedia'] = '<a href="#" class="btn btn-default t3js-filelist-update-metadata'
                . '" data-identifier="' . htmlspecialchars($fileOrFolderObject->getCombinedIdentifier())
                . '" data-file-uid="' . $fileProperties['uid']
                . '" data-filename="' . htmlspecialchars($fileOrFolderObject->getName())
                . '" title="' . $this->getLanguageService()->getLL('online_media_updater.update') . '">'
                . $this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL)->render() . '</a>';
        }
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        $languageService = $GLOBALS['LANG'];
        $languageService->includeLLFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        return $languageService;
    }
}
