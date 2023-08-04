<?php

namespace App\Service;

use App\Domain\Server\Data\Server;
use GuzzleHttp\Client;

class ServerInformationService
{
    public const BASE_URL = 'https://tgstation13.org';
    public const PUBLIC_LOGS = self::BASE_URL."/parsed-logs";
    public const ADMIN_LOGS = self::BASE_URL."/raw-logs";

    public static function getServerInfo(): array
    {
        $client = new Client([
            'base_uri' => 'https://tgstation13.org/',
            'timeout'  => 2.0,
        ]);
        $response = $client->get('/serverinfo.json');
        $data = json_decode($response->getBody(), true);
        return $data;
    }

    public static function getServerFromPort(int $port, ?array $data = null): ?Server
    {
        if(!$data) {
            $data = self::getServerInfo();
        }
        foreach($data as $server) {
            if(isset($server['serverdata'])) {
                if($port === $server['serverdata']['port']) {
                    $server['serverdata']['identifier'] = $server['identifier'];
                    return Server::fromArray($server['serverdata']);
                }
            }
        }
        return null;
    }

    public static function getCurrentRounds(?array $data = null): array
    {
        if(!$data) {
            $data = self::getServerInfo();
        }
        $rounds = [];
        foreach ($data as $s) {
            if(isset($s['version']) && $s['version'] === "/tg/Station 13") {
                if(isset($s['round_id'])) {
                    $rounds[] = (int) $s['round_id'];
                }
            }
        }
        return $rounds;
    }

}
