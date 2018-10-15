<?php
declare(strict_types=1);

use function DI\create;
use DI\ContainerBuilder;
use ExampleApp\HelloWorld;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder;
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    HelloWorld::class => create(HelloWorld::class)
]);

$container = $containerBuilder->build();

$helloWorld = $container->get(HelloWorld::class);
$helloWorld->announce();
