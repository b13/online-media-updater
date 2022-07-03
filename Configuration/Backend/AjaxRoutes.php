<?php

use B13\OnlineMediaUpdater\Controller\OnlineMediaUpdateController;

return [
    // Save a newly added online media
    'b13_online_media_updater' => [
        'path' => '/online-media/update',
        'target' => OnlineMediaUpdateController::class . '::updateAction',
    ],
];