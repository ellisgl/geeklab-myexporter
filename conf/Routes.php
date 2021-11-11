<?php

use App\Controllers\HomepageController;
use Symfony\Component\HttpFoundation\Response;

return [
    [
        'methods' => ['GET'],
        'path' => '/',
        'handler' => [HomepageController::class, 'index']
    ],
    [
        'methods' => ['GET'],
        'path' => '/hello-world',
        'handler' => [HomepageController::class, 'test']
    ],
    [
        'methods' => ['GET'],
        'path' => '/another-route',
        'handler' => function (Response $response) {
            $response->setContent('This works too.');
        }
    ],
];
