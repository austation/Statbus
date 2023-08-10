<?php

namespace App\Domain\Player\Service;

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class GetPlayerDiscordUsername
{
    private string $token;

    public function __construct(ContainerInterface $container)
    {
        $this->token = $container->get('settings')['auth']['discord']['botToken'];
    }

    public function getDiscordUser(int $id)
    {
        $client = new Client();
        $res = $client->request('GET', "https://discord.com/api/users/$id", [
            'headers' => [
                'Authorization' => 'Bot '.$this->token
            ]
        ]);
        return new DiscordResourceOwner(json_decode($res->getBody(), true));
    }

}
