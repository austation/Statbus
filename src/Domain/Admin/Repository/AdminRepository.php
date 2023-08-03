<?php

namespace App\Domain\Admin\Repository;

use App\Domain\Admin\Data\Admin;
use App\Repository\Repository;

class AdminRepository extends Repository
{
    public function getAdminRoster(): array
    {
        $playtime = $this->getAdminPlaytime();
        $admins = $this->connection->execute("SELECT a.ckey, a.rank, a.feedback, r.flags, p.lastseen
        FROM admin a
        LEFT JOIN admin_ranks r ON a.rank = r.rank
        LEFT JOIN player p ON a.ckey = p.ckey")->fetchAll('assoc');
        foreach($admins as &$a) {
            $a = Admin::fromArray($a);
            if(isset($playtime[$a->getCkey()])) {
                $a->setPlaytime($playtime[$a->getCkey()]);
            }
        }
        return $admins;
    }

    public function getAdminPlaytime(): array
    {
        $playtime = $this->connection->execute("SELECT a.ckey,
        t.job, sum(t.delta) as `minutes`
        FROM admin a
        LEFT JOIN role_time_log t on t.ckey = a.ckey
        WHERE t.job in ('Ghost','Living','Admin')
        AND t.datetime BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
        GROUP BY a.ckey, t.job")->fetchAll('assoc');
        $admins = array_flip(array_column($playtime, 'ckey'));
        foreach($playtime as $role) {
            if(is_int($admins[$role['ckey']])) {
                $admins[$role['ckey']] = null;
            }
            $admins[$role['ckey']][$role['job']] = (int) $role['minutes'] + (rand(1, 3) * 10);
        }
        return $admins;
    }

}
