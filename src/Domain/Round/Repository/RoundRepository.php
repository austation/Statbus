<?php

namespace App\Domain\Round\Repository;

use App\Domain\Round\Data\Round;
use App\Repository\Repository;
use App\Service\ServerInformationService;

class RoundRepository extends Repository
{
    public function getRound(int $id): Round
    {
        $servers = ServerInformationService::getServerInfo();
        $currentRounds = ServerInformationService::getCurrentRounds($servers);
        $data = $this->connection->execute("SELECT 
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
        WHERE id = ?
        ", [
            0 => $id
        ])->fetch('assoc');

        if(in_array($data['id'], $currentRounds)) {
            $round = new Round($data['id']);
            $round->setState('underway');
        } else {
            $round = $this->parseTimestamps($data);
            $round = new Round(...array_values($round));
        }
        $round->setServer(ServerInformationService::getServerFromPort($data['server_port'], $servers));
        return $round;
    }

}
