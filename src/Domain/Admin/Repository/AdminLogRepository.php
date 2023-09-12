<?php

namespace App\Domain\Admin\Repository;

use App\Domain\Admin\Data\Admin;
use App\Domain\Admin\Data\AdminLog;
use App\Domain\Player\Data\PlayerBadge;
use App\Repository\Repository;

class AdminLogRepository extends Repository
{
    public ?string $entityClass = AdminLog::class;

    public function getAdminLogs(int $page = 1, int $per_page = 60): array
    {
        $this->setPages((int) ceil($this->cell(
            "SELECT
            count(id) 
            FROM admin_log"
        ) / $per_page));

        $this->setResults(
            $this->run(
                "SELECT
                  L.id,
                  L.datetime,
                  L.round_id as round,
                  L.adminckey,
                  L.operation,
                  L.target,
                  L.log,
                  A.rank as a_rank,
                  T.rank as t_rank
                  FROM admin_log L
                  LEFT JOIN `admin` as A ON L.adminckey = A.ckey
                  LEFT JOIN `admin` as T ON L.target = T.ckey
                  ORDER BY L.datetime DESC
                  LIMIT ?,?",
                ($page * $per_page) - $per_page,
                $per_page
            )
        );
        return $this->getResults();
    }
    public function getAdminLogsForCkey(string $ckey, int $page = 1, int $per_page = 60): array
    {
        $this->setPages((int) ceil($this->cell(
            "SELECT
            count(id) 
            FROM admin_log"
        ) / $per_page));

        $this->setResults(
            $this->run(
                "SELECT
                  L.id,
                  L.datetime,
                  L.round_id as round,
                  L.adminckey,
                  L.operation,
                  L.target,
                  L.log,
                  A.rank as a_rank,
                  T.rank as t_rank
                  FROM admin_log L
                  LEFT JOIN `admin` as A ON L.adminckey = A.ckey
                  LEFT JOIN `admin` as T ON L.target = T.ckey
                  WHERE L.target = ?
                  ORDER BY L.datetime DESC
                  LIMIT ?,?",
                $ckey,
                ($page * $per_page) - $per_page,
                $per_page
            )
        );
        return $this->getResults();
    }

    public function getLatestAdmin(): AdminLog
    {
        $this->setResult($this->actualRow("SELECT
        L.id,
        L.datetime,
        L.round_id as round,
        L.adminckey,
        L.operation,
        L.target,
        L.log,
        A.rank as a_rank,
        T.rank as t_rank
        FROM admin_log L
        LEFT JOIN `admin` as A ON L.adminckey = A.ckey
        LEFT JOIN `admin` as T ON L.target = T.ckey
        WHERE L.operation = 'change admin rank'
        ORDER BY L.datetime DESC
        LIMIT 1"));
        return $this->getResult();
    }
}
