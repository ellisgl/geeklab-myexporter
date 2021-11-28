<?php

declare(strict_types=1);

namespace App;

use App\Authentication\AuthenticationInterface;
use App\Authentication\AuthenticationService;
use App\Authentication\NotLoggedInException;
use App\Core\Controllers\ErrorController;
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
$config = new GLConf(
    new ArrayConfDriver(__DIR__ . '/../config/config.php', __DIR__ . '/../config/'),
    [],
    ['keys_lower_case']
);
$config->init();
$environment = $config->get('env');

// Register the error handler.
$whoops = new Run;

if ('production' !== $environment) {
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
/** @var Session $session */
$session = $injector->make(Session::class);

// Share the configuration.
$injector->share($config);

/** @var AuthenticationService $authenticationService */
$authenticationService = $injector->make(AuthenticationService::class);

// Setup DB connections.
if (1 === $session->get('loggedIn')) {
    $dbConn = new PDO(
        'mysql:host=' . $session->get('dbh') . ';',
        $session->get('dbu'),
        $session->get('dbp'),
        [PDO::ATTR_PERSISTENT => false]
    );
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $injector->share($dbConn);
}

// Routing code:
// Load up routes for router, and initialize the dispatcher.
$routeDefinitionCallback = static function (RouteCollector $r) use ($config) {
    foreach ($config->get('routes') as $route) {
        $r->addRoute($route['methods'], $route['path'], $route['handler']);
    }
};
$dispatcher = simpleDispatcher($routeDefinitionCallback);

// Match the route.
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

// Add in some extra case handling and execute the route endpoint.
$errorController = $injector->make(ErrorController::class);

// @Todo Wrap in try-catch to deal with HTTP error codes.
/** @var Response $response */
switch ($routeInfo[0]) {
    case Dispatcher::METHOD_NOT_ALLOWED:
        $response = $errorController->error405();
        break;
    case Dispatcher::FOUND:
        try {
            if (is_array($routeInfo[1])) {
                // Controller class and method.
                [$className, $method] = $routeInfo[1];
                $vars = $routeInfo[2];
                $class = $injector->make($className);

                // We'll do a middleware the manual way, instead of the PSR-15 way.
                // If the controller class implements the Authentication interface, do an authentication check.
                if (in_array(AuthenticationInterface::class, class_implements($class), true)) {
                    $authenticationService->checkAuthenticated();
                }

                // Execute the action method.
                $response = $class->$method($vars);
            } elseif (is_callable($routeInfo[1])) {
                // Closure endpoint.
                $response = $injector->make(Response::class);
                $response->setContent($injector->execute($routeInfo[1]));
            } else {
                // We have something bad here.
                $response = $errorController->error405();
            }
        } catch (NotLoggedInException $e) {
            // Redirect to the login page.
            $response = new RedirectResponse('/');
        }
        break;
    case Dispatcher::NOT_FOUND:
    default:
        $response = $errorController->error404();
        break;
}

// Output the response to the viewer.
$response->send();
