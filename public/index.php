<?php
declare(strict_types=1);

use Relay\Relay;
use App\HelloWorld;
use function DI\get;
use function DI\create;
use DI\ContainerBuilder;
use Middlewares\FastRoute;
use FastRoute\RouteCollector;
use Middlewares\RequestHandler;
use function FastRoute\simpleDispatcher;
use Zend\Diactoros\ServerRequestFactory;


require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder;
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    HelloWorld::class => create(HelloWorld::class)
        ->constructor(get('Foo')),
        'Foo' => 'bar',
]);

$container = $containerBuilder->build();

$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/hello', HelloWorld::class);
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$requestHandler->handle(ServerRequestFactory::fromGlobals());

$helloWorld = $container->get(HelloWorld::class);
$helloWorld->announce();
