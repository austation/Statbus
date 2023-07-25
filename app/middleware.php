<?php

use Slim\App;
use Slim\Views\TwigMiddleware;
use Middlewares\TrailingSlash;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(TwigMiddleware::class);
    $app->add(new TrailingSlash(true));
    $app->addErrorMiddleware(true, true, true);
};
