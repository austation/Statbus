<?php

namespace App\Service;

use App\Domain\Server\Data\Server;
use GuzzleHttp\Client;

class ServerInformationService
{
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

}
