<?php

namespace App\Domain\Round\Service;

use App\Domain\Job\Data\JobBadge;
use App\Domain\Jobs\Data\Jobs;
use App\Domain\Round\Data\Round;
use App\Domain\Round\Data\TimelineKeys;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;

class ConstructTimelineData
{
    public static function buildFromData(array $data, Round $round): array
    {

        $timeline = [];
        if($data['stats']) {
            foreach($data['stats']['explosion']->getData() as $e) {
                $timeline[] = [
                    'timestamp' => new DateTime(substr($e['time'], 0, -2)),
                    'key' => TimelineKeys::EXPLOSION,
                    'string' => sprintf(
                        "Explosion with size (%s, %s, %s, %s, %s) at %s",
                        $e['dev'],
                        $e['heavy'],
                        $e['light'],
                        $e['flame'],
                        $e['flash'],
                        $e['area'],
                    ),
                    'coords' => $e['x'].','.$e['y'].','.$e['z']
                ];
            }
        }
        if(!empty($data['deaths'])) {
            foreach($data['deaths'] as $d) {
                $timeline[] = [
                    'key' => TimelineKeys::DEATH,
                    'timestamp' => $d->getTimestamp(),
                    'string' => sprintf(
                        "%s(%s) dies at %s",
                        $d->getName(),
                        $d->getPlayerBadge()->getCkey(),
                        $d->getLocation(),
                    ),
                    'coords' => $d->getCoords()
                ];
            }
        }

        $timeline[] = [
            'key' => TimelineKeys::ROUND_START,
            'timestamp' => $round->getInitDatetime(),
            'string' => 'Round Started'
        ];

        $timeline[] = [
            'key' => TimelineKeys::ROUND_START,
            'timestamp' => $round->getStartDatetime(),
            'string' => 'Round Started'
        ];

        $timeline[] = [
            'key' => TimelineKeys::ROUND_END,
            'timestamp' => $round->getEndDatetime(),
            'string' => 'Round Ended'
        ];
        // $tcomms = self::getTelecommsData($round);
        // array_shift($tcomms);
        // foreach($tcomms as $t) {
        //     $timeline[] = [
        //         'key' => TimelineKeys::TCOMMS,
        //         'timestamp' => new DateTime($t['ts']),
        //         'string' => $t['msg']
        //     ];
        // }

        $manifest = self::getManifestData($round);
        array_shift($manifest);
        foreach ($manifest as $t) {
            $data = explode(' \ ', $t['msg']);
            $data['byond_key'] = $data[0];
            $data['character'] = $data[1];
            $data['job'] = new JobBadge(Jobs::tryFrom($data[2]));
            $data['antagonist'] = $data[3] == 'NONE' ? false : new JobBadge(Jobs::tryFrom($data[3]));
            $data['latejoin'] = $data[4] == 'LATEJOIN' ? true : false;
            $timeline[] = [
                'key' => TimelineKeys::MANIFEST,
                'timestamp' => new DateTime($t['ts']),
                'string' => $t['msg'],
                'data' => $data
            ];
        }

        $dynamic = self::getDynamicData($round);
        array_shift($dynamic);
        foreach ($dynamic as $t) {
            $timeline[] = [
                'key' => TimelineKeys::DYNAMIC,
                'timestamp' => new DateTime($t['ts']),
                'string' => $t['msg']
            ];
        }

        $shuttle = self::getShuttleData($round);
        array_shift($shuttle);
        foreach ($shuttle as $t) {
            $timeline[] = [
                'key' => TimelineKeys::SHUTTLE,
                'timestamp' => new DateTime($t['ts']),
                'string' => $t['msg']
            ];
        }

        // var_dump(self::getNewsCasterData($round));

        usort($timeline, function ($a, $b) {
            $ad = $a['timestamp'];
            $bd = $b['timestamp'];

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? -1 : 1;
        });

        return $timeline;
    }

    private static function instantiateGuzzle(): Client
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(new GreedyCacheStrategy(null, 3000)), 'cache');

        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
            'handler' => $stack
        ]);
        return $client;
    }

    public static function getManifestData(Round $round): array
    {
        $client = self::instantiateGuzzle();
        $request = $client->request('GET', $round->getPublicLogFile('manifest.log.json'));
        $logs = $request->getBody();
        $logs = preg_replace('/}\n{/', '},{', "[$logs]");
        return json_decode($logs, true);
    }

    public static function getDynamicData(Round $round): array
    {
        $client = self::instantiateGuzzle();
        $request = $client->request('GET', $round->getPublicLogFile('dynamic.log.json'));
        $logs = $request->getBody();
        $logs = preg_replace('/}\n{/', '},{', "[$logs]");
        return json_decode($logs, true);
    }

    public static function getShuttleData(Round $round): array
    {
        $client = self::instantiateGuzzle();
        $request = $client->request('GET', $round->getPublicLogFile('shuttle.log.json'));
        $logs = $request->getBody();
        $logs = preg_replace('/}\n{/', '},{', "[$logs]");
        return json_decode($logs, true);
    }

    public static function getTelecommsData(Round $round): array
    {
        $client = self::instantiateGuzzle();
        $request = $client->request('GET', $round->getPublicLogFile('telecomms.log.json'));
        $logs = $request->getBody();
        $logs = preg_replace('/}\n{/', '},{', "[$logs]");
        return json_decode($logs, true);
    }

    public static function getNewsCasterData(Round $round): array
    {
        $client = self::instantiateGuzzle();
        $request = $client->request('GET', $round->getPublicLogFile('newscaster.json'));
        $logs = $request->getBody();
        // $logs = preg_replace('/}\n{/', '},{', "[$logs]");
        return json_decode($logs, true);
    }


}
