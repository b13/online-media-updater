<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Online Media Updater',
    'description' => 'Update YouTube/Vimeo metadata in fileadmin',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'B13\\OnlineMediaUpdater\\' => 'Classes/',
        ],
    ],
];
