<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get("/", \App\Controller\Home\HomeController::class)->setName("home");
    $app->get("/logout", \App\Controller\Auth\LogoutController::class)->setName("logout");

    //Authentication via Discord
    $app->group("/auth", function (RouteCollectorProxy $app) {
        $app->get("/discord", \App\Controller\Auth\StartDiscordAuthenticationController::class)->setName("auth.discord");
        $app->get("/discord/check", \App\Controller\Auth\FinishDiscordAuthenticationController::class)->setName("auth.discord.check");

        $app->get("/tgforum", \App\Controller\Auth\StartForumAuthenticationController::class)->setName("auth.forum");
        $app->get("/tgforum/success", \App\Controller\Auth\FinishForumAuthenticationController::class)->setName("auth.forum.check");
    });

    $app->group("/bans", function (RouteCollectorProxy $app) {
        $app->get("", \App\Controller\Ban\UserBanController::class)->setName("user.bans");
        $app->get("/{id:[0-9]+}", \App\Controller\Ban\ViewBanController::class)->setName("ban.view");
    });

    $app->group("/rounds", function (RouteCollectorProxy $app) {
        $app->get("/{id:[0-9]+}", \App\Controller\Round\RoundViewController::class)->setName("round.single");
    });

};
