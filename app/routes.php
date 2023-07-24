<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get("/home", \App\Controller\HomeController::class.':home')->setName("home");
    $app->get("/login", \App\Controller\HomeController::class.':login')->setName("login");
};
