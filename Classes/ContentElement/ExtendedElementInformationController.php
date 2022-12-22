<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\ContentElement;

use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ExtendedElementInformationController extends \TYPO3\CMS\Backend\Controller\ContentElement\ElementInformationController
{
    public function isOnlineMedia($fileObject): bool
    {
        $registeredHelpers = $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'];

        return array_key_exists($fileObject->getExtension(), $registeredHelpers);
    }

    /**
     * Compiles the whole content to be outputted, which is then set as content to the moduleTemplate
     * There is a hook to do a custom rendering of a record.
     */
    public function hookedContent(): string
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $queryParams = $request->getQueryParams();
        $uid = $queryParams['uid'] ?? '';

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@online-media-updater/Updater.js');
        $pageRenderer->addInlineLanguageLabelFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        // Rendering of the output via fluid
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Templates')]);
        $view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Partials')]);

        // EXT:online_media_updater - set path to custom template for the preview button
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:online_media_updater/Resources/Private/Templates/ElementInformation.html'
        ));

        // Access check is not necessary, is checked in the ElementInformationController->mainAction()
        $view->assign('accessAllowed', true);
        $view->assignMultiple($this->getPageTitle());
        $view->assignMultiple($this->getPreview());
        $view->assignMultiple($this->getPropertiesForTable());
        $view->assignMultiple($this->getReferences($request, $uid));
        $view->assign('returnUrl', GeneralUtility::sanitizeLocalUrl($queryParams['returnUrl'] ?? ''));
        $view->assign('maxTitleLength', $this->getBackendUser()->uc['titleLen'] ?? 20);

        return $view->render();
    }
}
