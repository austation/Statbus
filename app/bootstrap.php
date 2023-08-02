<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;

require_once(__DIR__."/encoding.php");
require_once(__DIR__."/../vendor/autoload.php");

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();
$app = Bridge::create($container);

return $container->get(App::class);
