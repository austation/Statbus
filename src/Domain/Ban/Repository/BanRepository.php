<?php

namespace App\Domain\Ban\Repository;

use App\Repository\Repository;
use App\Domain\Ban\Data\Ban;
use App\Service\ServerInformationService;

class BanRepository extends Repository
{
    private $columns = "SELECT 
        ban.id,
        round_id as `round`,
        ban.server_ip,
        ban.server_port,
        GROUP_CONCAT(role SEPARATOR ', ') as `role`,
        null as `banIds`,
        ban.bantime,
        ban.expiration_time as `expiration`,
        ban.reason,
        ban.ckey,
        c.rank as `c_rank`,
        ban.a_ckey,
        a.rank as `a_rank`,
        ban.unbanned_ckey,
        ban.unbanned_datetime,
        u.rank as `u_rank`,
        CASE
            WHEN expiration_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, bantime, expiration_time)
            ELSE 0
        END AS `minutes`,
        round.initialize_datetime AS round_time,
        ban.edits
        FROM ban
        LEFT JOIN `round` ON round_id = round.id
        LEFT JOIN `admin` AS c ON c.ckey = ban.ckey	
        LEFT JOIN `admin` AS a ON a.ckey = ban.a_ckey
        LEFT JOIN `admin` AS u ON u.ckey = ban.unbanned_ckey";

    public function getPublicBans()
    {
        return $this->run("$this->columns ORDER BY bantime DESC;");
    }

    public function getBansForCkey($ckey)
    {
        $bans = [];
        $rawBans = $this->connection->execute("$this->columns WHERE ban.ckey = ? 
        GROUP BY bantime, ckey, `server_port`
        ORDER BY bantime DESC", [0 => $ckey])->fetchAll('assoc');

        foreach ($rawBans as $ban) {
            $ban['server'] = null;
            $ban = $this->parseTimestamps($ban);
            $bans[] = Ban::fromArray($ban);
        }
        return $bans;
    }

    public function getBanById($id)
    {
        $ban = $this->connection->execute("SELECT 
            ban.id,
            ban.round_id as `round`,
            ban.server_ip,
            ban.server_port,
            GROUP_CONCAT(r.role SEPARATOR ', ') as `role`,
            GROUP_CONCAT(r.id SEPARATOR ', ') as `banIds`,
            ban.bantime,
            ban.expiration_time as `expiration`,
            ban.reason,
            ban.ckey,
            c.rank as `c_rank`,
            ban.a_ckey,
            a.rank as `a_rank`,
            ban.unbanned_ckey,
            ban.unbanned_datetime,
            u.rank as `u_rank`,
            CASE
                WHEN ban.expiration_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, ban.bantime, ban.expiration_time)
                ELSE 0
            END AS `minutes`,
            CASE 
                WHEN ban.expiration_time < NOW() THEN 0
                WHEN ban.unbanned_ckey IS NOT NULL THEN 0
                ELSE 1 
            END as `active`,
            ban.edits
            FROM ban
            LEFT JOIN `round` ON round_id = round.id
            LEFT JOIN `admin` AS c ON c.ckey = ban.ckey	
            LEFT JOIN `admin` AS a ON a.ckey = ban.a_ckey
            LEFT JOIN `admin` AS u ON u.ckey = ban.unbanned_ckey
            INNER JOIN ban r ON r.bantime = ban.bantime AND r.ckey = ban.ckey
            WHERE ban.id = ? 
            GROUP BY ban.bantime, ban.ckey, `server_port`", [0 => $id])->fetch('assoc');
        $ban = $this->parseTimestamps($ban);
        $ban['server'] = ServerInformationService::getServerFromPort($ban['server_port']);
        return Ban::fromArray($ban);
    }

    public function getPlayerStanding(string $ckey)
    {
        $this->setResults(
            $this->run(
                "SELECT B.role, 
                B.id,
                B.expiration_time
                FROM ban B
                WHERE ckey = ?
                AND ((B.expiration_time > NOW() AND B.unbanned_ckey IS NULL)
                OR (B.expiration_time IS NULL AND B.unbanned_ckey IS NULL))",
                $ckey
            ),
            false
        );
        return $this;
    }

}
