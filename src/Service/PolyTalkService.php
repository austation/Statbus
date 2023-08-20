<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use League\Flysystem\Adapter\Local;

class PolyTalkService
{
    public const BASE_URL = 'https://tgstation13.org';
    public const PUBLIC_LOGS = self::BASE_URL."/parsed-logs";

    public static function getPolyLine(): string
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(new GreedyCacheStrategy(null, 300)), 'cache');

        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
            'handler' => $stack
        ]);
        $server = pick('basil', 'sybil', 'manuel', 'terry');
        try {
            $poly = $client->request('GET', self::PUBLIC_LOGS.'/'.$server.'/data/npc_saves/Poly.json');
            $poly = json_decode((string) $poly->getBody(), true);
            return pick($poly['phrases']);
        } catch (\Guzzle\Http\Exception\ConnectException $e) {
            $response = json_encode((string)$e->getResponse()->getBody());
        }
    }

}
