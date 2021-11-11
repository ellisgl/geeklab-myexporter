<?php

declare(strict_types=1);

use App\Menu\ArrayMenuReader;
use App\Menu\MenuReader;
use App\Template\FrontendRenderer;
use App\Core\Template\TwigRenderer;
use App\Core\Renderer;
use App\Template\FrontendTwigRenderer;
use Auryn\Injector;
use GeekLab\Conf\Driver\ArrayConfDriver;
use GeekLab\Conf\GLConf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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

// Stuff for Session.
$injector->share(Session::class);

// Template render
$injector->delegate('Twig_Environment', function () use ($injector) {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
    return new Environment($loader);
});
$injector->alias(Environment::class, 'Twig_Environment');
$injector->alias(Renderer::class, TwigRenderer::class);

// Template engine extending.
$injector->alias(FrontendRenderer::class, FrontendTwigRenderer::class);
$injector->alias(MenuReader::class, ArrayMenuReader::class);
$injector->share(ArrayMenuReader::class);

return $injector;
