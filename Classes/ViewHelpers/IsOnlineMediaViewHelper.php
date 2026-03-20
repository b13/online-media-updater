<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\ViewHelpers;

use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IsOnlineMediaViewHelper extends AbstractViewHelper
{
    public function __construct(private readonly ResourceFactory $resourceFactory) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('fileUid', 'int', 'File uid', true);
    }

    public function render(): bool
    {
        try {
            $file = $this->resourceFactory->getFileObject($this->arguments['fileUid']);
            return array_key_exists($file->getExtension(), $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers'] ?? []);
        } catch (FileDoesNotExistException) {
        }
        return false;
    }
}
