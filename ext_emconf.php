<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Online Media Updater',
    'version' => '2.0.0',
    'description' => 'Update YouTube/Vimeo metadata in fileadmin',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.9.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'B13\\OnlineMediaUpdater\\' => 'Classes/',
        ],
    ],
];
