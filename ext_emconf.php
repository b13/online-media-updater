<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Online Media Updater',
    'description' => 'Update YouTube/Vimeo metadata in fileadmin',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'B13\\OnlineMediaUpdater\\' => 'Classes/',
        ],
    ],
];
