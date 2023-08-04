<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get("/", \App\Controller\Home\HomeController::class)->setName("home");
    $app->get("/logout", \App\Controller\Auth\LogoutController::class)->setName("logout");

    $app->get("/changelog", \App\Controller\Home\MarkdownController::class)->setName("changelog")->setArgument('file', 'changelog.md')->setArgument('title', 'Changelog');
    $app->get("/privacy", \App\Controller\Home\MarkdownController::class)->setName("privacy")->setArgument('file', 'privacy-policy.md')->setArgument('title', 'Privacy Policy');

    //Authentication Controllers
    $app->group("/auth", function (RouteCollectorProxy $app) {
        //Via Discord
        $app->get("/discord", \App\Controller\Auth\StartDiscordAuthenticationController::class)->setName("auth.discord");
        $app->get("/discord/check", \App\Controller\Auth\FinishDiscordAuthenticationController::class)->setName("auth.discord.check");

        //Via forums
        $app->get("/tgforum", \App\Controller\Auth\StartForumAuthenticationController::class)->setName("auth.forum");
        $app->get("/tgforum/success", \App\Controller\Auth\FinishForumAuthenticationController::class)->setName("auth.forum.check");
    });

    //Bans
    $app->group("/bans", function (RouteCollectorProxy $app) {
        $app->get("", \App\Controller\Ban\UserBanController::class)->setName("user.bans");
        $app->get("/{id:[0-9]+}", \App\Controller\Ban\ViewBanController::class)->setName("ban.view");
    });

    //Rounds
    $app->group("/rounds", function (RouteCollectorProxy $app) {
        $app->get("/{id:[0-9]+}", \App\Controller\Round\RoundViewController::class)->setName("round.single");
    });

    //Info pages
    $app->group("/info", function (RouteCollectorProxy $app) {
        $app->get("/admins", \App\Controller\Info\AdminRosterController::class)->setName("admins");
    });


    //Ticket pages
    $app->group("/tickets", function (RouteCollectorProxy $app) {
        $app->get("[/page/{page:[0-9]+}]", \App\Controller\Tickets\TicketListingController::class)->setName("user.tickets");
        $app->get("/{round:[0-9]+}/{ticket:[0-9]+}", \App\Controller\Tickets\TicketViewerController::class)->setName("user.ticket");
    });

};
