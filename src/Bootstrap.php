<?php

declare(strict_types=1);

namespace App;

// use App\Controllers\HomepageController;
use App\Controllers\HomepageController;
use Inhere\Route\Dispatcher\Dispatcher;
use Inhere\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$environment = 'development';

/**
 * Register the error handler
 */
$whoops = new Run;

if ($environment !== 'production') {
    $whoops->pushHandler(new PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        echo 'Todo: Friendly error page and send an email to the developer';
    });
}

$whoops->register();

// Set up the request object.
$request = new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

// Set up the response object.
$response = new Response();

// Set up router.
$router = new Router();

// Load in routes
$routes = include('../conf/Routes.php');

foreach($routes as $route) {
    $router->map(
        $route['methods'],
        $route['path'],
        $route['handler'],
        !empty($route['params']) && is_array($route['params']) ? $route['params'] : []
    );
}

// Do the routing.
$router->dispatch(new Dispatcher(['actionSuffix' => '']));

// Output the response to the viewer.
$response->send();
