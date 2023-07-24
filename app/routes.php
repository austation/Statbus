<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get("/", \App\Controller\HomeController::class.':home')->setName("home");
};
