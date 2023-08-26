<?php

namespace App\Domain\Death\Repository;

use App\Domain\Death\Data\Death;
use App\Repository\Repository;
use App\Service\ServerInformationService;

class DeathRepository extends Repository
{
    public ?string $entityClass = Death::class;

    private array $columns = [
        'd.id',
        'd.pod',
        'd.x_coord',
        'd.y_coord',
        'd.z_coord',
        'd.mapname',
        'd.server_ip',
        'd.server_port',
        'd.round_id',
        'd.tod',
        'd.job',
        'd.special',
        'd.name',
        'd.byondkey',
        'd.laname',
        'd.lakey',
        'd.bruteloss',
        'd.brainloss',
        'd.fireloss',
        'd.oxyloss',
        'd.toxloss',
        'd.cloneloss',
        'd.staminaloss',
        'd.last_words',
        'd.suicide',
        'pl.rank AS d_rank',
        'la.rank AS l_rank'
    ];

    private array $joins = [
        'LEFT JOIN `admin` AS pl ON pl.ckey = d.byondkey',
        'LEFT JOIN `admin` AS la ON la.ckey = d.lakey',
    ];

    private array $where = [
    ];

    public function getDeathsForRound(int $round): array
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);
        $where = implode("\n AND ", [...$this->where, 'd.round_id = ?']);
        $query = sprintf("SELECT %s FROM death d %s \nWHERE %s
      ORDER BY d.tod", $cols, $joins, $where);
        $this->setResults(
            $this->run(
                $query,
                $round
            ),
        );
        return $this->getResults();
    }

    public function getDeaths(int $page = 1, int $per_page = 60): array
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);

        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);

        $currentRounds = "('".implode("','", $currentRounds)."')";
        $where = implode("\n AND ", ["d.round_id NOT IN $currentRounds"]);


        $query = sprintf("SELECT %s FROM death d %s \nWHERE %s
        ORDER BY d.tod DESC", $cols, $joins, $where);
        $this->setResults(
            $this->run(
                $query
            ),
        );
        return $this->getResults();
    }

}
