<?php

use App\Middleware\ExceptionMiddleware;
use Slim\App;
use Slim\Views\TwigMiddleware;
use Middlewares\TrailingSlash;
use Slim\Views\Twig;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(TwigMiddleware::create($app, $app->getContainer()->get(Twig::class)));
    $app->add(ExceptionMiddleware::class);
    $app->add(new TrailingSlash());
    // $app->add(new WhoopsMiddleware());
};
