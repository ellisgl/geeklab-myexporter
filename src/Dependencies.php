<?php declare(strict_types = 1);

use Auryn\Injector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$injector = new Injector;

// $injector->alias(Request::class, 'Http\HttpRequest');
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

// $injector->alias(Response::class, 'Http\HttpResponse');
$injector->share(Response::class);

return $injector;
