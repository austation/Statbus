<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;

require_once(__DIR__."/encoding.php");
require_once(__DIR__."/../vendor/autoload.php");

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();
$app = Bridge::create($container);

(require __DIR__ . '/routes.php')($app);
(require __DIR__ . '/middleware.php')($app);

return $app;


// $app::setRouteCollectorConfiguration($routerConfiguration);

// $cache = new CacheImplementation();
