<?php

namespace App\Service;

use App\Domain\Server\Data\Server;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Symfony\Component\Yaml\Yaml;

class ServerInformationService
{
    // unsure why this isn't in config
    public const BASE_URL = 'https://logs.austation.net';
    public const PUBLIC_LOGS = self::BASE_URL . "/parsed-logs";
    public const ADMIN_LOGS = self::BASE_URL . "/raw-logs";

    public static function getServerInfo(): ?array
    {
        // TODO: interface wth AuAPI to get current round ID detail and server info

        $data = Yaml::parseFile(__DIR__ . '/../../assets/servers.json');

        if (isset($data['refreshtime'])) {
            unset($data['refreshtime']);
        }
        return $data;
    }

    public static function getServerFromPort(int $port, ?array $data = null): ?Server
    {
        if (!$data) {
            $data = self::getServerInfo();
        }
        foreach ($data as $server) {
            if (isset($server['serverdata'])) {
                if ($port === $server['serverdata']['port']) {
                    if (isset($server['identifier'])) {
                        $server['serverdata']['identifier'] = $server['identifier'];
                    } else {
                        $server['serverdata']['identifier'] = explode(' ', $server['serverdata']['servername'])[0];
                    }
                    return Server::fromArray($server['serverdata']);
                }
            }
        }
        return null;
    }

    public static function getServerFromName(string $name, ?array $data = null): ?Server
    {
        if (!$data) {
            $data = self::getServerInfo();
        }
        foreach ($data as $server) {
            if (isset($server['identifier'])) {
                if ($name === $server['identifier']) {
                    if (isset($server['identifier'])) {
                        $server['serverdata']['identifier'] = $server['identifier'];
                    } else {
                        $server['serverdata']['identifier'] = explode(' ', $server['serverdata']['servername'])[0];
                    }
                    return Server::fromArray($server['serverdata']);
                }
            }
        }
        return null;
    }

    public static function getCurrentRounds(?array $data = null): array
    {
        if (!$data) {
            $data = self::getServerInfo();
        }
        $rounds = [];
        foreach ($data as $s) {
            if (isset($s['round_id'])) {
                $rounds[] = (int) $s['round_id'];
            }
        }
        return $rounds;
    }
}
