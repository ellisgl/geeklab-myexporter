<?php

declare(strict_types=1);

namespace App;

use App\Core\Exceptions\NotLoggedInException;
use Auryn\Injector;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use GeekLab\Conf\Driver\ArrayConfDriver;
use GeekLab\Conf\GLConf;
use PDO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

use function FastRoute\simpleDispatcher;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

// Configuration
$config = new GLConf(new ArrayConfDriver(__DIR__ . '/../config/config.php', __DIR__ . '/../config/'));
$config->init();
$environment = $config->get('env');

/**
 * Register the error handler
 */
$whoops = new Run;

if ($environment !== 'production') {
    $whoops->pushHandler(new PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
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
/** @var Session $session */
$session = $injector->make(Session::class);

// Share the configuration.
$injector->share($config);

// Setup DB connections.
if (1 === $session->get('loggedIn')) {
    $dbConn = new PDO(
        'mysql:host=' . $session->get('dbh') . ';',
        $session->get('dbu'),
        $session->get('dbp'),
        [PDO::ATTR_PERSISTENT => false]
    );

    $injector->share($dbConn);
}

// Load up routes for router, and initialize the dispatcher.
$routeDefinitionCallback = static function (RouteCollector $r) use ($config) {
    foreach ($config->get('routes') as $route) {
        $r->addRoute($route['METHODS'], $route['PATH'], $route['HANDLER']);
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
        try {
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
        } catch (NotLoggedInException $e) {
            // Redirect to the login page.
            $response = new RedirectResponse('/');
        }
        break;
}

// Output the response to the viewer.
$response->send();
