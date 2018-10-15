<?php
declare(strict_types=1);

use Relay\Relay;
use App\HelloWorld;
use function DI\get;
use function DI\create;
use DI\ContainerBuilder;
use Middlewares\FastRoute;
use Zend\Diactoros\Response;
use FastRoute\RouteCollector;
use Middlewares\RequestHandler;
use function FastRoute\simpleDispatcher;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;


require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder;
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    HelloWorld::class => create(HelloWorld::class)
        ->constructor(get('Foo'), get('Response')),
        'Foo' => 'bar',
        'Response' => function() {
            return new Response();
        },
]);

$container = $containerBuilder->build();

$routes = simpleDispatcher(function (RouteCollector $r) {
    $r->get('/hello', HelloWorld::class);
});

$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);

// $helloWorld = $container->get(HelloWorld::class);
// $helloWorld->announce();
