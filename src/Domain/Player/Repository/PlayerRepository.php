<?php

namespace App\Domain\Player\Repository;

use App\Domain\Player\Data\Player;
use App\Enum\Jobs;
use App\Repository\Repository;
use PDO;

class PlayerRepository extends Repository
{
    public ?string $entityClass = Player::class;

    public function getPlayerByCkey(string $ckey): Player
    {
        $data = $this->actualRow(
            "SELECT
            p.ckey,
            p.firstseen,
            p.lastseen,
            p.firstseen_round_id as firstSeenRound,
            p.lastseen_round_id as lastSeenRound,
            p.accountjoindate as accountJoined,
            a.rank,
            r.flags
            FROM player p
            LEFT JOIN `admin` a ON a.ckey = p.ckey
            LEFT JOIN admin_ranks r ON SUBSTRING_INDEX(a.rank,'+',1) = r.rank
            WHERE p.ckey = ?",
            [$ckey]
        );
        $this->setResult($data);
        return $this->getResult();
    }
    public function getPlayerRecentPlaytime(string $ckey): array
    {
        $jobs = "('".implode("','", array_column(Jobs::cases(), 'value'))."')";
        $data = $this->db->run(
            "SELECT sum(t.delta) as `minutes`, t.job FROM role_time_log t
            WHERE t.ckey = ?
            AND t.job in $jobs
            AND t.datetime BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
            GROUP BY t.job",
            $ckey
        );
        foreach($data as &$d) {
            $job = Jobs::tryFrom($d->job);
            $d->minutes = (int) $d->minutes + (rand(1, 3) * 10);
            $d->background = $job->getColor();
        }
        $this->setResults($data, true);
        return $this->getResults();
    }

}
// SELECT a.ckey,
//         t.job, sum(t.delta) as `minutes`
//         FROM admin a
//         LEFT JOIN role_time_log t on t.ckey = a.ckey
//         WHERE t.job in ('Ghost','Living','Admin')
//         AND t.datetime BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
//         GROUP BY a.ckey, t.job
