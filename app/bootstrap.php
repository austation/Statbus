<?php

function pick($list)
{
    if (is_string($list)) {
        $list = explode(',', $list);
    } elseif (is_object($list)) {
        $list = json_decode(json_encode($list), true);
    }
    return $list[floor(rand(0, count($list) - 1))];
}

// https://stackoverflow.com/a/41910059
function getYoutubeEmbedUrl($url)
{
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    return 'https://www.youtube.com/embed/' . $youtube_id ;
}

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
