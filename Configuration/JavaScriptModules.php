<?php

return [
    'dependencies' => [
        'core',
        'backend',
    ],
    'tags' => [
        'online-media-updater.updater',
    ],
    'imports' => [
        '@online-media-updater/' => 'EXT:online_media_updater/Resources/Public/JavaScript/Backend/',
    ],
];
