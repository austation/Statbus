<?php

namespace App\Domain\Discord\Repository;

use App\Repository\Repository;

class DiscordRepository extends Repository
{
    public function getCkeyFromLinkedDiscordAccount(string $id): string
    {
        return $this->connection
            ->execute('SELECT * FROM discord_links WHERE discord_id = :id', ['id' => $id])
            ->fetch('assoc')['ckey'];
    }
}
