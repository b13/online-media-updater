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

use B13\OnlineMediaUpdater\ContentElement\ExtendedElementInformationController;
use TYPO3\CMS\Backend\Controller\ContentElement\ElementInformationController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InfoAddOnlineMediaUpdater
{
    protected IconFactory $iconFactory;
    protected UriBuilder $uriBuilder;
    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected ExtendedElementInformationController $element;

    public function __construct()
    {
        if (!class_exists('TYPO3\CMS\Backend\Template\ModuleTemplateFactory')) {
            // TYPO3 10
            $this->element = GeneralUtility::makeInstance(ExtendedElementInformationController::class);
            return;
        }

        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        $this->element = GeneralUtility::makeInstance(ExtendedElementInformationController::class, $this->iconFactory, $this->uriBuilder, $this->moduleTemplateFactory);
    }

    public function render(string $type, ElementInformationController $element): string
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        return $this->element->mainAction($request)->getBody()->getContents();
    }

    public function isValid(string $type, ElementInformationController $element): bool
    {
        return $this->element->isOnlineMedia();
    }
}
