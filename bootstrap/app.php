<?php

/**
 * Minimalist PHP - Basic PHP Structure
 *
 * @package Minimalist PHP
 * @author Endru Reza <endrureza@gmail.com>
 */
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Load DotEnv
 */
$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();

/**
 * Register The Error Handler
 */
$whoops = new Run;
if (getenv('APP_ENV') !== 'production') {
    $whoops->pushHandler(new PrettyPageHandler);
} else {
    $whoops->pushHandler(function ($e) {
        echo "Dang Dude, There's an error ! Aw shucks ! I need to stay late again !";
    });
}
$whoops->register();

/**
 * Register HTTP
 */
$request = Request::createFromGlobals();

/**
 * Register Dependency Injector
 */
$container_builder = new ContainerBuilder;
$container         = $container_builder
    ->addDefinitions(
        require_once __DIR__ . '/../config/app.php'
    )
    ->useAnnotations(true)
    ->build();

/**
 * Register Twig
 */
$loader = new Twig_Loader_Filesystem($container->get('view')['paths']);
$twig   = new Twig_Environment($loader);

/**
 * Register Monolog
 */
$logger = new Logger('mvplog');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/error.log'));
$logger->pushHandler(new FirePHPHandler());

/**
 * Register Routes
 */
$route_definition = function (RouteCollector $r) {
    require __DIR__ . '/../routes/routes.php';
};

$dispatcher = FastRoute\simpleDispatcher($route_definition);
$route      = $dispatcher->dispatch(
    $request->getMethod(),
    $request->getPathInfo()
);

switch ($route[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo $twig->render('errors/404.twig');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo $twig->render('errors/405.twig');
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $route[1];
        $parameters = $route[2];
        $container->call($controller, $parameters);
        break;
}
