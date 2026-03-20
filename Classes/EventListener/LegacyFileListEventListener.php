<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\EventListener;

use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Filelist\Event\ProcessFileListActionsEvent;

#[AsEventListener(identifier: 'b13/online-media-updater/legacy-filelist-listener')]
final class LegacyFileListEventListener
{
    public function __construct(protected IconFactory $iconFactory) {}

    public function __invoke(ProcessFileListActionsEvent $event): void
    {
        if ((new Typo3Version())->getMajorVersion() > 13) {
            return;
        }
        $actionItems = $event->getActionItems();

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@online-media-updater/LegacyUpdater.js');
        $pageRenderer->addInlineLanguageLabelFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        $fileOrFolderObject = $event->getResource();
        if ($fileOrFolderObject instanceof File) {
            $fileProperties = $fileOrFolderObject->getProperties();
            $extension = $fileProperties['extension'] ?? '';

            $registeredHelpers = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] ?? [];
            if (!array_key_exists($extension, $registeredHelpers)) {
                return;
            }

            $button = GeneralUtility::makeInstance(LinkButton::class);
            $button->setHref('#')
                ->setClasses('dropdown-item dropdown-item-spaced')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:online_media_updater/Resources/Private/Language/locallang.xlf:online_media_updater.update'))
                ->setIcon($this->iconFactory->getIcon('actions-refresh', IconSize::SMALL))
                ->setDataAttributes([
                    'filename' => htmlspecialchars($fileOrFolderObject->getName()),
                    'file-uid' => $fileProperties['uid'],
                    't3js-filelist-update-metadata' => 1,
                ]);
            $actionItems['updateOnlineMedia'] = $button;
        }

        $event->setActionItems($actionItems);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
