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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @todo: Remove when v11 support was dropped
 */
class ExtendedElementInformationController extends \TYPO3\CMS\Backend\Controller\ContentElement\ElementInformationController
{
    public function isOnlineMedia(): bool
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
        $this->init($request);

        if (!$this->fileObject instanceof File) {
            return false;
        }

        return array_key_exists($this->fileObject->getExtension(), $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] ?? []);
    }

    /**
     * Compiles the whole content to be outputted, which is then set as content to the moduleTemplate
     * There is a hook to do a custom rendering of a record.
     *
     * @param ServerRequestInterface $request
     */
    protected function main(ServerRequestInterface $request): void
    {
        $content = '';
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/OnlineMediaUpdater/Backend/UpdaterV11');
        $pageRenderer->addInlineLanguageLabelFile('EXT:online_media_updater/Resources/Private/Language/locallang.xlf');

        // Rendering of the output via fluid
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths([GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Templates')]);
        $view->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Partials')]);

        // EXT:online_media_updater - set path to custom template for the preview button
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:online_media_updater/Resources/Private/TemplateOverrides/Templates/ContentElement/ElementInformationV11.html'
        ));

        if ($this->access) {
            $view->assign('accessAllowed', true);
            $view->assignMultiple($this->getPageTitle());
            $view->assignMultiple($this->getPreview());
            $view->assignMultiple($this->getPropertiesForTable());
            $view->assignMultiple($this->getReferences($request));
            $view->assign('returnUrl', GeneralUtility::sanitizeLocalUrl($request->getQueryParams()['returnUrl'] ?? ''));
            $view->assign('maxTitleLength', $this->getBackendUser()->uc['titleLen'] ?? 20);
            $content .= $view->render();
        } else {
            $content .= $view->render();
        }

        $this->moduleTemplate->setContent($content);
    }
}
