<?php

declare(strict_types=1);

use App\Menu\ArrayMenuReader;
use App\Menu\MenuReader;
use App\Template\FrontendRenderer;
use App\Core\Template\TwigRenderer;
use App\Page\PageReader;
use App\Page\FilePageReader;
use App\Core\Renderer;
use App\Template\FrontendTwigRenderer;
use Auryn\Injector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Loader\FilesystemLoader as Twig_Loader_Filesystem;
use Twig\Environment;

// Create the injector.
$injector = new Injector();

// Stuff for Request.
$injector->share(Request::class);
$injector->define(
    Request::class,
    [
        ':query'      => $_GET,
        ':request'    => $_POST,
        ':attributes' => [],
        ':cookies'    => $_COOKIE,
        ':files'      => $_FILES,
        ':server'     => $_SERVER,
    ]
);

// Stuff for Response.
$injector->share(Response::class);

// Template render
//$injector->define(
//    'Mustache_Engine',
//    [
//        ':options' => [
//            'loader' => new Mustache_Loader_FilesystemLoader(
//                dirname(__DIR__) . '/templates',
//                ['extension' => '.html',]
//            ),
//        ],
//    ]
//);

$injector->delegate('Twig_Environment', function () use ($injector) {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
    return new Environment($loader);
});

$injector->alias(Environment::class, 'Twig_Environment');
$injector->alias(Renderer::class, TwigRenderer::class);
$injector->alias(FrontendRenderer::class, FrontendTwigRenderer::class);

// Template engine extending.
$injector->alias(MenuReader::class, ArrayMenuReader::class);
$injector->share(ArrayMenuReader::class);

// Pager Reader stuff.
$injector->define(
    FilePageReader::class,
    [
        ':pageFolder' => __DIR__ . '/../pages',
    ]
);
$injector->alias(PageReader::class, FilePageReader::class);
$injector->share(FilePageReader::class);

return $injector;
