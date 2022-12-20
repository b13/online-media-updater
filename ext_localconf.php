<?php

use B13\OnlineMediaUpdater\Hooks\InfoAddOnlineMediaUpdater;

defined('TYPO3') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'][] = InfoAddOnlineMediaUpdater::class;
});
