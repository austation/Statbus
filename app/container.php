<?php

use App\Controller;
use App\Domain\User\Repository\UserRepository;
use App\Extension\Twig\WebpackAssetLoader;
use App\Middleware\ExceptionMiddleware;
use App\Repository\Repository;
use Cake\Database\Connection;
use DI\Container;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\MarkdownConverter;
use Nyholm\Psr7\Factory\Psr17Factory;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

return [

    //Settings
    'settings' => function () {
        return require __DIR__ . "/settings.php";
    },

    //Application
    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Register routes
        (require __DIR__ . '/routes.php')($app);

        // Register middleware
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },

    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    //Route parsing
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container
        ->get(App::class)
        ->getRouteCollector()
        ->getRouteParser();
    },

    //Controller
    Controller::class => function (ContainerInterface $container) {
        return new Controller($container);
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

        // var_dump($settings, $options);

        $twig = Twig::create($settings["paths"], $options);

        $loader = $twig->getLoader();
        $publicPath = (string) $config["public"];
        if ($loader instanceof FilesystemLoader) {
            $loader->addPath($publicPath, "public");
        }

        $twig->getEnvironment()->addGlobal("debug", $config["debug"]);
        $twig->getEnvironment()->addGlobal("app", $config["app"]);
        $twig->getEnvironment()->addGlobal("flash", $session->getFlashBag()->all());
        $twig->getEnvironment()->addGlobal("user", $container->get(User::class));

        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $twig->addExtension(new WebpackAssetLoader($options));
        $twig->addRuntimeLoader(new class () implements RuntimeLoaderInterface {
            public function load($class)
            {
                $config = [
                    'default_attributes' => [
                        Table::class => [
                            'class' => 'table table-bordered',
                        ],
                    ],
                ];
                if (MarkdownRuntime::class === $class) {
                    $environment = new Environment($config);
                    $environment->addExtension(new CommonMarkCoreExtension());
                    $environment->addExtension(new DefaultAttributesExtension());
                    $environment->addExtension(new GithubFlavoredMarkdownExtension());
                    return new MarkdownConverter($environment);
                }
            }
        });
        $twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat('Y-m-d H:i:s', '%a minutes');
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('UTC');

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

    Connection::class => function (ContainerInterface $container) {
        return new Connection($container->get('settings')['db'], $container->get(EasyDB::class));
    },

    Repository::class => function (ContainerInterface $container) {
        return new Repository($container->get(Connection::class), $container->get(EasyDB::class));
    },

    EasyDB::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];
        $dsn = sprintf(
            "mysql:host=%s:%s;dbname=%s",
            $settings['host'],
            $settings['port'],
            $settings['database']
        );
        return Factory::create($dsn, $settings['username'], $settings['password'], $settings['flags']);
    },

    User::class => function (ContainerInterface $containerInterface) {
        $userRepository = new UserRepository($containerInterface->get(Connection::class), $containerInterface->get(EasyDB::class));
        $session = $containerInterface->get(Session::class);
        $ckey = $session->get('ckey');
        if(!$ckey) {
            return null;
        }
        $user = $userRepository->getUserByCkey($ckey);
        $user->setSource($session->get('authSource'));
        return $user;
    },
    ExceptionMiddleware::class => function (ContainerInterface $containerInterface) {
        return new ExceptionMiddleware($containerInterface);
    }

];
