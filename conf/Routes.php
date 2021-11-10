<?php

use Symfony\Component\HttpFoundation\Response;

/** @var Response $response */

return [
    [
        'methods' => ['GET'],
        'path' => '/',
        'handler' => static function () use ($response) {
            $response->setContent('Router: Hello, World 1!');
        }
    ],
    [
        'methods' => ['GET'],
        'path' => '/hello-world',
        'handler' => static function () use ($response) {
            $response->setContent('Router: Hello, World 2!');
        }
    ],
    [
        'methods' => ['GET'],
        'path' => '/another-route',
        'handler' => static function () use ($response) {
            $response->setContent('This works too.');
        }
    ],
];
