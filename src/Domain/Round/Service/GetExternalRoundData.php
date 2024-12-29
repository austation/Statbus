<?php

namespace App\Domain\Round\Service;

use App\Domain\Round\Data\Round;
use App\Domain\Stat\Data\Stat;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;

class GetExternalRoundData
{
    public static function getRoundEndData(Round $round): Stat
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(new GreedyCacheStrategy(null, 3000)), 'cache');

        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
            'handler' => $stack
        ]);
        try {
            $request = $client->request('GET', $round->getPublicLogFile('round_end_data.json'));
        } catch (Exception $e) {
            return new Stat(-1, $round->getStart(), $round->getId(), 'sb_who', 'associative', 1, false);
        }
        $stat = new Stat(-1, $round->getStart(), $round->getId(), 'sb_who', 'associative', 1, (string) $request->getBody());
        return $stat;
    }

    public static function getRoundEndReport(Round $round): Stat
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(new GreedyCacheStrategy(null, 3000)), 'cache');

        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
            'handler' => $stack
        ]);
        $request = $client->request('GET', $round->getPublicLogFile('round_end_data.html'));
        $stat = new Stat(-1, $round->getStartDatetime(), $round->getId(), 'sb_roundend', 'associative', 1, (string) $request->getBody(), false);
        return $stat;
    }

    public static function getPolyLine(): string
    {

        $server = pick('austation');
        try {
            $poly = $client->request('GET', self::PUBLIC_LOGS.'/'.$server.'/data/npc_saves/Poly.json');
            $poly = json_decode((string) $poly->getBody(), true);
            return pick($poly['phrases']);
        } catch (\Guzzle\Http\Exception\ConnectException $e) {
            $response = json_encode((string)$e->getResponse()->getBody());
        }
    }

    public static function getLogList(Round $round): array
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(new GreedyCacheStrategy(null, 3000)), 'cache');

        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
            'handler' => $stack
        ]);
        $request = $client->request('GET', $round->getPublicLogFile("?index_format=json"));
        $list = [];
        foreach(json_decode($request->getBody(), true) as $l) {
            if(!str_ends_with($l['name'], '.html.gz')) {
                $l['name'] = str_replace('.gz', '', $l['name']);
                $l['name'] = sprintf("%s/%s", $round->getPublicLogs(), $l['name']);
                $file = explode('/', $l['name']);
                $l['file'] = end($file);
                $list[] = $l;
            }
        }
        return $list;
    }

}
