<?php

namespace App\Domain\Stat\Repository;

use App\Domain\Stat\Data\Stat;
use App\Repository\Repository;

class StatRepository extends Repository
{
    public ?string $entityClass = Stat::class;

    public function getStatsForRound(int $round, array $stats): array
    {
        $stats = "('".implode("','", $stats)."')";
        $query = "SELECT f.id, f.datetime, f.round_id, f.key_name, f.key_type, f.version, f.json FROM feedback f WHERE f.round_id = ? AND f.key_name in $stats";
        $this->setResults($this->run($query, $round));
        $tmp = [];
        foreach($this->getResults() as $r) {
            $tmp[$r->getKey()] = $r;
        }
        return $tmp;
    }

}
