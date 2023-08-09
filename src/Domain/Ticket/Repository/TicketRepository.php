<?php

namespace App\Domain\Ticket\Repository;

use App\Domain\Ticket\Data\Ticket;
use App\Repository\Repository;

class TicketRepository extends Repository
{
    public ?string $entityClass = Ticket::class;

    public function getTicketsByCkey(string $ckey, int $page = 1, int $per_page = 60): self
    {
        $this->setPages((int) ceil($this->db->cell(
            "SELECT
          count(t.id) 
          FROM ticket t
          WHERE t.action = 'Ticket Opened' 
          AND (t.recipient = ? OR t.sender = ?);",
            $ckey,
            $ckey
        ) / $per_page));
        $this->setResults(
            $this->db->run(
                "SELECT 
                t.id,
                t.server_ip as serverIp, 
                t.server_port as port,
                t.round_id as `round`,
                t.ticket,
                t.action,
                t.message,
                t.timestamp,
                t.recipient as r_ckey,
                t.sender as s_ckey,
                r.rank as r_rank,
                s.rank as s_rank,
                (SELECT `action` 
                  FROM ticket 
                  WHERE t.ticket = ticket AND t.round_id = round_id 
                  ORDER BY id DESC LIMIT 1)
                as `status`,
                (SELECT COUNT(id) 
                  FROM ticket 
                  WHERE t.ticket = ticket 
                  AND t.round_id = round_id) 
                as `replies`,
                t.urgent
                FROM ticket t
                LEFT JOIN `admin` AS r ON r.ckey = t.recipient	
                LEFT JOIN `admin` AS s ON s.ckey = t.sender
                WHERE t.action = 'Ticket Opened'
                AND (t.recipient = ? OR t.sender = ?)
                AND t.round_id != 0
                GROUP BY t.id
                ORDER BY `timestamp` DESC
                LIMIT ?, ?",
                $ckey,
                $ckey,
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );
        return $this;
    }

    public function getTickets(int $page = 1, int $per_page = 60): self
    {
        $this->setPages((int) ceil($this->db->cell(
            "SELECT
          count(t.id) 
          FROM ticket t
          WHERE t.action = 'Ticket Opened' 
          "
        ) / $per_page));
        $this->setResults(
            $this->db->run(
                "SELECT 
                t.id,
                t.server_ip as serverIp, 
                t.server_port as port,
                t.round_id as `round`,
                t.ticket,
                t.action,
                t.message,
                t.timestamp,
                t.recipient as r_ckey,
                t.sender as s_ckey,
                r.rank as r_rank,
                s.rank as s_rank,
                (SELECT `action` 
                  FROM ticket 
                  WHERE t.ticket = ticket AND t.round_id = round_id 
                  ORDER BY id DESC LIMIT 1)
                as `status`,
                (SELECT COUNT(id) 
                  FROM ticket 
                  WHERE t.ticket = ticket 
                  AND t.round_id = round_id) 
                as `replies`,
                t.urgent
                FROM ticket t
                LEFT JOIN `admin` AS r ON r.ckey = t.recipient	
                LEFT JOIN `admin` AS s ON s.ckey = t.sender
                WHERE t.action = 'Ticket Opened'
                AND t.round_id != 0
                GROUP BY t.id
                ORDER BY `timestamp` DESC
                LIMIT ?, ?",
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );
        return $this;
    }

    public function getTicketsForRound(int $round, int $page = 1, int $per_page = 60): self
    {
        $this->setPages((int) ceil($this->db->cell(
            "SELECT
          count(t.id) 
          FROM ticket t
          WHERE t.action = 'Ticket Opened'
          AND t.round_id = ?",
            $round
        ) / $per_page));
        $this->setResults(
            $this->db->run(
                "SELECT 
                t.id,
                t.server_ip as serverIp, 
                t.server_port as port,
                t.round_id as `round`,
                t.ticket,
                t.action,
                t.message,
                t.timestamp,
                t.recipient as r_ckey,
                t.sender as s_ckey,
                r.rank as r_rank,
                s.rank as s_rank,
                (SELECT `action` 
                  FROM ticket 
                  WHERE t.ticket = ticket AND t.round_id = round_id 
                  ORDER BY id DESC LIMIT 1)
                as `status`,
                (SELECT COUNT(id) 
                  FROM ticket 
                  WHERE t.ticket = ticket 
                  AND t.round_id = round_id) 
                as `replies`,
                t.urgent
                FROM ticket t
                LEFT JOIN `admin` AS r ON r.ckey = t.recipient	
                LEFT JOIN `admin` AS s ON s.ckey = t.sender
                WHERE t.action = 'Ticket Opened'
                AND t.round_id = ?
                GROUP BY t.id
                ORDER BY `timestamp` DESC
                LIMIT ?, ?",
                $round,
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );
        return $this;
    }

    public function getSingleTicket(int $round, int $ticket): self
    {
        $this->setResults(
            $this->db->run(
                "SELECT 
                    t.id,
                    t.server_ip as serverIp, 
                    t.server_port as port,
                    t.round_id as `round`,
                    t.ticket,
                    t.action,
                    t.message,
                    t.timestamp,
                    t.recipient as r_ckey,
                    t.sender as s_ckey,
                    r.rank as r_rank,
                    s.rank as s_rank,
                    (SELECT `action` 
                  FROM ticket 
                  WHERE t.ticket = ticket AND t.round_id = round_id 
                  ORDER BY id DESC LIMIT 1)
                as `status`,
                    (SELECT COUNT(id) 
                      FROM ticket 
                      WHERE t.ticket = ticket 
                      AND t.round_id = round_id)
                    as `replies`,
                    t.urgent
                    FROM ticket t
                    LEFT JOIN `admin` AS r ON r.ckey = t.recipient	
                    LEFT JOIN `admin` AS s ON s.ckey = t.sender
                    WHERE t.round_id = ?
                    AND t.ticket = ? 
                    AND t.round_id != 0
                    GROUP BY t.id
                    ORDER BY `timestamp` ASC",
                $round,
                $ticket
            )
        );

        return $this;
    }
}
