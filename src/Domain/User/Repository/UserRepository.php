<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Data\User;
use App\Repository\Repository;

class UserRepository extends Repository
{
    public function getUserByCkey(string $ckey): User
    {
        $user = $this->connection
        ->execute("SELECT p.ckey, SUBSTRING_INDEX(SUBSTRING_INDEX(a.rank, '+', 1), ',', -1) as rank, r.flags, a.feedback
        FROM `player` p
        LEFT JOIN `admin` a ON a.ckey = p.ckey
        LEFT JOIN admin_ranks r ON a.rank = r.rank
        WHERE p.ckey = :ckey", ['ckey' => $ckey])
        ->fetch('assoc');
        return User::fromArray($user);
    }
}
