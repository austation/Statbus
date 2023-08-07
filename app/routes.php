<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

return function (App $app) {
    $app->get("/", \App\Controller\Home\HomeController::class)->setName("home");
    $app->get("/logout", \App\Controller\Auth\LogoutController::class)->setName("logout");

    $app->get("/changelog", \App\Controller\Home\MarkdownController::class)->setName("changelog")->setArgument('file', 'changelog.md')->setArgument('title', 'Changelog');

    $app->get("/privacy", \App\Controller\Home\MarkdownController::class)->setName("privacy")->setArgument('file', 'privacy-policy.md')->setArgument('title', 'Privacy Policy');

    $settings = $app->getContainer()->get('settings')['app'];
    $app->redirect('/discord', $settings['discord'], 301)->setName('discord');

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

    //Players
    $app->group("/player", function (RouteCollectorProxy $app) {
        $app->get("/{ckey:[a-z0-9@]+}", \App\Controller\Player\ViewPlayerController::class)->setName("player");
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

    $app->group("/tgdb", function (RouteCollectorProxy $app) {
        $app->get("", \App\Controller\TGDB\TGDBController::class)->setName("tgdb");
        $app->get("/tlp", \App\Controller\Home\MarkdownController::class)->setName("tgdb.tlp")->setArgument('file', 'tlp_guide.md')->setArgument('title', 'Guide to TLP');

        //TGDB Tickets
        $app->get("/tickets[/page/{page:[0-9]+}]", \App\Controller\TGDB\Tickets\TGDBTicketListingController::class)->setName("tgdb.tickets");

        $app->get("/tickets/{round:[0-9]+}[/page/{page:[0-9]+}]", \App\Controller\TGDB\Tickets\TGDBTicketRoundListingController::class)->setName("tgdb.tickets.round");

        $app->get("/tickets/{round:[0-9]+}/{ticket:[0-9]+}", \App\Controller\TGDB\Tickets\TGDBTicketViewerController::class)->setName("tgdb.ticket");

        //TGDB Players
        $app->get("/player/{ckey:[a-z0-9@]+}", \App\Controller\TGDB\Player\TGDBPlayerViewController::class)->setName("tgdb.player");

        //TGDB Bans
        $app->get("/bans/{ckey:[a-z0-9@]+}[/page/{page:[0-9]+}]", \App\Controller\TGDB\Ban\TGDBBansByCkeyController::class)->setName("tgdb.bans.player");

        $app->get("/ban/{id:[0-9]+}", \App\Controller\TGDB\Ban\TGDBBanViewController::class)->setName("tgdb.ban.view");


    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('require', 'ADMIN');
        $response = $handler->handle($request);
        return $response;
    });

};
