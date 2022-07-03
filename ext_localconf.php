<?php

use B13\OnlineMediaUpdater\Hooks\AddOnlineMediaUpdater;

defined('TYPO3') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['fileList']['editIconsHook'][] = AddOnlineMediaUpdater::class;
});
