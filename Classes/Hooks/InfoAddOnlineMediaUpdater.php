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
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InfoAddOnlineMediaUpdater
{
    protected IconFactory $iconFactory;
    protected UriBuilder $uriBuilder;
    protected ModuleTemplateFactory $moduleTemplateFactory;
    protected ExtendedElementInformationController $element;
    protected ResourceFactory $resourceFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $this->moduleTemplateFactory = GeneralUtility::makeInstance(ModuleTemplateFactory::class);
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        $this->element = GeneralUtility::makeInstance(
            ExtendedElementInformationController::class,
            $this->iconFactory,
            $this->uriBuilder,
            $this->moduleTemplateFactory,
            $this->resourceFactory
        );
    }

    public function render(string $type, ElementInformationController $element): string
    {
        // There is no init function any more. Here I first had to add the setter and getter in ElementInformationController.
        $this->element->setFileObject($element->getFileObject());
        $this->element->setTable($element->getTable());
        $this->element->setRow($element->getRow());
        $this->element->setType($type);

        return $this->element->hookedContent();
    }

    public function isValid(string $type, ElementInformationController $element): bool
    {
        return $this->element->isOnlineMedia($element->getFileObject());
    }
}
