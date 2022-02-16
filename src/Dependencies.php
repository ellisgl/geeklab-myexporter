<?php

declare(strict_types=1);

use App\Authentication\AuthenticationService;
use App\Core\Template\TwigRenderer;
use App\Core\Renderer;
use Auryn\Injector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Loader\FilesystemLoader as Twig_Loader_Filesystem;
use Twig\Environment;

// Create the injector.
$injector = new Injector();

// Create the request object so we can share it with our controller actions.
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

// Create the response object so we can output to our users.
$injector->share(Response::class);

// Create the session object, so we can do things like keeping people logged in.
$injector->share(Session::class);

// Create the authentication object, so people can login.
$injector->share(AuthenticationService::class);

// Create the template engine object, so we can easily make things pretty for our users.
$injector->delegate('Twig_Environment', function () use ($injector) {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
    return new Environment($loader);
});
$injector->alias(Environment::class, 'Twig_Environment');
$injector->alias(Renderer::class, TwigRenderer::class);

// Return the injector, so it can be used to inject our goodies.
return $injector;
