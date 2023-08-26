<?php

namespace App\Domain\Stat\Repository;

use App\Domain\Stat\Data\Stat;
use App\Repository\Repository;

class StatRepository extends Repository
{
    public ?string $entityClass = Stat::class;

    public function listStatsForRound(int $round): array
    {
        $query = "SELECT f.key_name FROM feedback f WHERE f.round_id = ? ORDER BY f.key_name ASC";
        $this->setResults($this->run($query, $round), true);
        return $this->getResults();
    }

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

    public function getRoundStat(int $round, string $stat): Stat
    {
        $query = "SELECT f.id, f.datetime, f.round_id, f.key_name, f.key_type, f.version, f.json FROM feedback f WHERE f.round_id = ? AND f.key_name = ?";
        $this->setResult($this->actualRow($query, [$round, $stat]));
        return $this->getResult();
    }

    public function getRandomEntryForKey(string $stat): self
    {
        $query = "SELECT f.id, f.datetime, f.round_id, f.key_name, f.key_type, f.version, f.json FROM feedback f WHERE f.key_name = ? ORDER BY RAND() LIMIT 1";
        $this->setResult($this->actualRow($query, [$stat]));
        return $this;
    }

}
