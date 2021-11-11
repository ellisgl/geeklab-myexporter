<?php

declare(strict_types=1);

namespace App;

use Auryn\Injector;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

use function FastRoute\simpleDispatcher;

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

// Configure and init dependency injection.
/** @var Injector $injector */
$injector = include('Dependencies.php');
/** @var Request $request */
$request = $injector->make(Request::class);
/** @var Response $response */
$response = $injector->make(Response::class);

// Load up routes for router, and initialize the dispatcher.
$routeDefinitionCallback = static function (RouteCollector $r) {
    $routes = include('../conf/Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route['methods'], $route['path'], $route['handler']);
    }
};
$dispatcher = simpleDispatcher($routeDefinitionCallback);

// Match the route.
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

// Add in some extra case handling and execute the route endpoint.
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response->setContent('404 - Page not found');
        $response->setStatusCode(404);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('405 - Method not allowed');
        $response->setStatusCode(405);
        break;
    case Dispatcher::FOUND:
        if (is_array($routeInfo[1])) {
            // Controller class and method.
            [$className, $method] = $routeInfo[1];
            $vars = $routeInfo[2];
            $class = $injector->make($className);
            $class->$method($vars);
        } elseif (is_callable($routeInfo[1])) {
            // Closure endpoint.
            $injector->execute($routeInfo[1]);
        } else {
            // We have something bad here.
            $response->setContent('405 - Method not allowed');
            $response->setStatusCode(405);
        }
        break;
}

// Output the response to the viewer.
$response->send();
