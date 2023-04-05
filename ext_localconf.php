<?php

use B13\OnlineMediaUpdater\Hooks\InfoAddOnlineMediaUpdater;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

call_user_func(function () {
    // @todo: Remove when v11 support was dropped
    if ((GeneralUtility::makeInstance(Typo3Version::class))->getMajorVersion() < 12) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'][] = InfoAddOnlineMediaUpdater::class;
    }
});
