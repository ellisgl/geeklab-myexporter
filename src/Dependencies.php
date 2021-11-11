<?php

declare(strict_types=1);

use App\Core\Renderer;
use App\Core\Template\MustacheRenderer;
use Auryn\Injector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Create the injector.
$injector = new Injector();

// Stuff for Request.
$injector->share(Request::class);
$injector->define(
    Request::class,
    [
        ':get'        => $_GET,
        ':post'       => $_POST,
        ':attributes' => [],
        ':cookies'    => $_COOKIE,
        ':files'      => $_FILES,
        ':server'     => $_SERVER,
    ]
);

// Stuff for Response.
$injector->share(Response::class);

// Template render
$injector->alias(Renderer::class, MustacheRenderer::class);
$injector->define(
    'Mustache_Engine',
    [
        ':options' => [
            'loader' => new Mustache_Loader_FilesystemLoader(
                dirname(__DIR__) . '/templates',
                ['extension' => '.html',]
            ),
        ],
    ]
);

return $injector;
