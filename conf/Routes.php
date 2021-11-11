<?php

use App\Controllers\HomepageController;
use App\Controllers\PageController;
use Symfony\Component\HttpFoundation\Response;

return [
    [
        'methods' => ['GET'],
        'path' => '/',
        'handler' => [HomepageController::class, 'index']
    ],
    [
        'methods' => ['GET'],
        'path' => '/another-route',
        'handler' => static function (Response $response) {
            $response->setContent('This works too.');
        }
    ],
    [
        'methods' => ['GET'],
        'path' => '/{slug}',
        'handler' => [PageController::class, 'show']
    ],
];
