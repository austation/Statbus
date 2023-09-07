<?php

namespace App\Domain\Round\Repository;

use App\Domain\Round\Data\Round;
use App\Repository\Repository;
use App\Service\ServerInformationService;

class RoundRepository extends Repository
{
    public ?string $entityClass = Round::class;

    public function getRound(int $id): Round
    {

        $query = "SELECT
        r.id,
        r.initialize_datetime,
        r.start_datetime,
        r.shutdown_datetime,
        r.end_datetime,
        r.server_ip,
        r.server_port,
        r.commit_hash,
        r.game_mode,
        r.game_mode_result,
        r.end_state,
        r.shuttle_name,
        r.map_name,
        r.station_name
        FROM round r
        WHERE r.id = ?";

        $data = $this->actualRow($query, [$id]);
        $this->setResult($data);
        $round = $this->getResult();
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);

        if(in_array($round->getId(), $currentRounds)) {
            $round->setState('underway');
        }
        return $round;
    }

    public function getRecentRounds(): array
    {
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);
        $currentRounds = "('".implode("','", $currentRounds)."')";
        $query = "SELECT
        r.id,
        r.initialize_datetime,
        r.start_datetime,
        r.shutdown_datetime,
        r.end_datetime,
        r.server_ip,
        r.server_port,
        r.commit_hash,
        r.game_mode,
        r.game_mode_result,
        r.end_state,
        r.shuttle_name,
        r.map_name,
        r.station_name
        FROM round r
        WHERE r.id NOT IN $currentRounds
        ORDER BY r.id DESC
        LIMIT 0, 12";
        $this->setResults($this->run($query));
        return $this->getResults();
    }

    public function getRounds(int $page = 1, int $per_page = 60): array
    {
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);
        $currentRounds = "('".implode("','", $currentRounds)."')";
        $this->setPages((int) ceil($this->cell("SELECT count(id) FROM round WHERE id NOT IN $currentRounds") / $per_page));
        $query = "SELECT
        r.id,
        r.initialize_datetime,
        r.start_datetime,
        r.shutdown_datetime,
        r.end_datetime,
        r.server_ip,
        r.server_port,
        r.commit_hash,
        r.game_mode,
        r.game_mode_result,
        r.end_state,
        r.shuttle_name,
        r.map_name,
        r.station_name
        FROM round r
        WHERE r.id NOT IN $currentRounds
        ORDER BY r.id DESC
        LIMIT ?,?";
        $this->setResults($this->run(
            $query,
            ($page * $per_page) - $per_page,
            $per_page
        ));
        return $this->getResults();
    }

    public function getRoundsForCkey(string $ckey, int $page = 1, int $per_page = 60): array
    {
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);
        $currentRounds = "('".implode("','", $currentRounds)."')";
        $this->setPages((int) ceil($this->cell("SELECT count(c.round_id) FROM connection_log c WHERE c.round_id NOT IN $currentRounds AND c.ckey = ?", $ckey) / $per_page));
        $query = "SELECT
        r.id,
        r.initialize_datetime,
        r.start_datetime,
        r.shutdown_datetime,
        r.end_datetime,
        r.server_ip,
        r.server_port,
        r.commit_hash,
        r.game_mode,
        r.game_mode_result,
        r.end_state,
        r.shuttle_name,
        r.map_name,
        r.station_name
        FROM connection_log c
        LEFT JOIN round r ON c.round_id = r.id
        WHERE c.ckey = ?
        AND r.id NOT IN $currentRounds
        ORDER BY r.id DESC
        LIMIT ?,?";
        $this->setResults($this->run(
            $query,
            $ckey,
            ($page * $per_page) - $per_page,
            $per_page
        ));
        return $this->getResults();
    }

    public function roundSearch(string $term): array
    {
        return $this->run(
            "SELECT id as round, station_name FROM round WHERE station_name LIKE ? OR id LIKE ?    
        ORDER BY id DESC
        LIMIT 0,5",
            '%' . $this->db->escapeLikeValue($term) . '%',
            '%' . $this->db->escapeLikeValue($term) . '%'
        );
    }
}
