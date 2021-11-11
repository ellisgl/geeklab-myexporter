<?php

use App\Controllers\HomepageController;
use App\Controllers\LoginController;
use Symfony\Component\HttpFoundation\Response;

return [
    'routes' => [
        [
            'methods' => ['GET'],
            'path'    => '/',
            'handler' => [HomepageController::class, 'index']
        ],
        [
            'methods' => ['GET'],
            'path'    => '/another-route',
            'handler' => static function (Response $response) {
                $response->setContent('This works too.');
            }
        ],
        [
            'methods' => ['GET', 'POST'],
            'path'    => '/login',
            'handler' => [LoginController::class, 'index']
        ],
        // Wildcards need to be at the bottom.
    ]
];
