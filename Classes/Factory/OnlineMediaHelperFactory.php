<?php

declare(strict_types=1);

/**
 * This file is part of TYPO3 CMS-based extension "online-media-updater" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace B13\OnlineMediaUpdater\Factory;

use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\VimeoHelper;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OnlineMediaHelperFactory
{
    public function createOnlineMediaHelper(string $fileExtension): VimeoHelper|YouTubeHelper|null
    {
        return match ($fileExtension) {
            'youtube' => $this->createYoutubeHelper(),
            'vimeo' => $this->createVimeoHelper(),
            default => null,
        };
    }

    protected function createYoutubeHelper(): YouTubeHelper
    {
        return GeneralUtility::makeInstance(YouTubeHelper::class, 'youtube');
    }

    protected function createVimeoHelper(): VimeoHelper
    {
        return GeneralUtility::makeInstance(VimeoHelper::class, 'vimeo');
    }
}
