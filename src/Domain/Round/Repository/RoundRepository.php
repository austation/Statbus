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
        WHERE id = ?";

        $data = $this->actualRow($query, [$id]);
        $this->setResult($data);
        $round = $this->getResult();
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);

        if(in_array($round->getId(), $currentRounds)) {
            $round = new Round($round->getId());
            $round->setState('underway');
        }
        return $round;
    }
}
