<?php

namespace App\Domain\Admin\Repository;

use App\Domain\Admin\Data\Admin;
use App\Repository\Repository;

class AdminRepository extends Repository
{
    public function getAdminRoster(): array
    {
        $admins = $this->connection->execute("SELECT a.ckey, a.rank, a.feedback, r.flags, p.lastseen
        FROM admin a
        LEFT JOIN admin_ranks r ON a.rank = r.rank
        LEFT JOIN player p ON a.ckey = p.ckey")->fetchAll('assoc');
        foreach($admins as &$a) {
            $a = Admin::fromArray($a);
        }
        return $admins;
    }

}
