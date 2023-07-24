<?php

use App\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Loader\FilesystemLoader;

return [

    //Settings
    'settings' => function () {
        return require __DIR__ . "/settings.php";
    },

    //Application
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        return $app;
    },

    //Response
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    //Route parsing
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container
        ->get(App::class)
        ->getRouteCollector()
        ->getRouteParser();
    },

    //Request
    Request::class => function (ContainerInterface $container) {
        return $container->get("Request");
    },

    //Controller
    Controller::class => function (ContainerInterface $container) {
        return new Controller(
            $container->get(ResponseFactoryInterface::class),
            $container->get(Twig::class)
        );
    },

    //TwigMiddleware
    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class
        );
    },

    //Twig
    Twig::class => function (ContainerInterface $container) {
        $session = $container->get(Session::class);
        $config = (array) $container->get("settings");
        $settings = $config["twig"];
        $options = $settings["options"];
        $options["cache"] = $options["cache_enabled"]
        ? $options["cache_path"]
        : false;

        $twig = Twig::create($settings["paths"], $options);

        $loader = $twig->getLoader();
        $publicPath = (string) $config["public"];
        if ($loader instanceof FilesystemLoader) {
            $loader->addPath($publicPath, "public");
        }
        $twig->getEnvironment()->addGlobal("debug", $config["debug"]);
        $twig->getEnvironment()->addGlobal("app", $config["app"]);
        $twig->getEnvironment()->addGlobal("flash", $session->getFlashBag()->all());
        // $twig->getEnvironment()->addGlobal("user", $session->get("user"));

        return $twig;
    },

    //Session
    Session::class => function (ContainerInterface $container) {
        $settings = $container->get("settings")["session"];
        if (PHP_SAPI === "cli") {
            return new Session(new MockArraySessionStorage());
        } else {
            return new Session(new NativeSessionStorage($settings));
        }
    },

];
