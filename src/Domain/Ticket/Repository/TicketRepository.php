<?php

namespace App\Domain\Ticket\Repository;

use App\Domain\Ticket\Data\Ticket;
use App\Repository\Repository;
use App\Service\ServerInformationService;

class TicketRepository extends Repository
{
    public ?string $entityClass = Ticket::class;

    private array $columns = [
      't.id',
      't.server_ip as serverIp',
      't.server_port as port',
      't.round_id as `round`',
      't.ticket',
      't.action',
      't.message',
      't.timestamp',
      't.recipient as r_ckey',
      't.sender as s_ckey',
      'r.rank as r_rank',
      's.rank as s_rank',
      '(SELECT `action` FROM ticket WHERE id = c.last_id LIMIT 1) as `status`',
      'c.replies as `replies`',
    ];

    private array $joins = [
      'LEFT JOIN `admin` AS r ON r.ckey = t.recipient',
      'LEFT JOIN `admin` AS s ON s.ckey = t.sender',
      'LEFT JOIN (SELECT round_id, ticket, COUNT(id) as `replies`, max(id) as `last_id` FROM ticket GROUP BY round_id, ticket) as c on (c.round_id = t.round_id and c.ticket = t.ticket)'
    ];

    private array $where = [
      't.action = "Ticket Opened"',
      't.round_id != 0'
    ];

    public function getTickets(int $page = 1, int $per_page = 60): self
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);
        $where = implode("\n AND ", $this->where);
        $query = sprintf("SELECT %s FROM ticket t %s \nWHERE %s
        ORDER BY t.timestamp DESC LIMIT ?, ?", $cols, $joins, $where);
        $pagesQuery = sprintf("SELECT count(t.id) FROM ticket t WHERE %s", $where);
        $this->setPages((int) ceil($this->cell($pagesQuery) / $per_page));
        $this->setResults(
            $this->run(
                $query,
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );
        return $this;
    }

    public function getTicketFeed(int $page = 1, int $per_page = 60): self
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);
        $where = implode("\n AND ", ['t.round_id != 0']);
        $query = sprintf("SELECT %s FROM ticket t %s \nWHERE %s
        ORDER BY t.timestamp DESC LIMIT ?, ?", $cols, $joins, $where);
        $pagesQuery = sprintf("SELECT count(t.id) FROM ticket t WHERE %s ORDER BY t.timestamp DESC", $where);
        $this->setPages((int) ceil($this->cell($pagesQuery) / $per_page));
        $this->setResults(
            $this->run(
                $query,
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );
        return $this;
    }

    public function getTicketsSinceDate(string $date): self
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);
        $where = implode("\n AND ", ['t.timestamp > ?','t.round_id != 0']);
        $query = sprintf("SELECT %s FROM ticket t %s \nWHERE %s ORDER BY t.timestamp DESC", $cols, $joins, $where);
        $this->setResults(
            $this->run(
                $query,
                $date
            ),
        );
        return $this;
    }

    public function getTicketsByCkey(string $ckey, int $page = 1, int $per_page = 60): self
    {
        $pagesQuery = "SELECT
        ticket.id as results
        FROM
        ticket
        LEFT JOIN `admin` AS r ON r.ckey = ticket.recipient
        LEFT JOIN `admin` AS s ON s.ckey = ticket.sender
        LEFT JOIN (SELECT round_id, ticket, COUNT(id) as `replies`, max(id) as `last_id` FROM ticket GROUP BY round_id, ticket) as c on (c.round_id = ticket.round_id and c.ticket = ticket.ticket)
        INNER JOIN ticket AS first_tickets ON first_tickets.round_id = ticket.round_id
            AND first_tickets.ticket = ticket.ticket
            AND first_tickets.action = 'Ticket Opened'
            AND ticket.round_id != 0
		WHERE ticket.recipient = ? OR ticket.sender = ?
            GROUP BY ticket.round_id, ticket.ticket;";
        $query = "SELECT
        first_tickets.id,
        first_tickets.server_ip as `serverIp`,
        first_tickets.server_port as `port`,
        first_tickets.round_id as `round`,
        first_tickets.ticket,
        first_tickets.action,
        first_tickets.message,
        first_tickets.timestamp,
        first_tickets.recipient as r_ckey,
        first_tickets.sender as s_ckey,
        r.rank as `r_rank`,
        s.rank as `s_rank`,
        (SELECT `action` FROM ticket WHERE id = c.last_id LIMIT 1) as `status`,
        c.replies as `replies`
    FROM
        ticket
        LEFT JOIN (SELECT round_id, ticket, COUNT(id) as `replies`, max(id) as `last_id` FROM ticket GROUP BY round_id, ticket) as c on (c.round_id = ticket.round_id and c.ticket = ticket.ticket)
        INNER JOIN ticket AS first_tickets ON first_tickets.round_id = ticket.round_id
            AND first_tickets.ticket = ticket.ticket
            AND first_tickets.action = 'Ticket Opened'
            AND ticket.round_id != 0
        LEFT JOIN `admin` AS r ON r.ckey = first_tickets.recipient
        LEFT JOIN `admin` AS s ON s.ckey = first_tickets.sender
		WHERE ticket.recipient = ? OR ticket.sender = ?
            GROUP BY ticket.round_id , ticket.ticket
            ORDER BY id DESC
            LIMIT ?, ?;";
        $this->setPages(count($this->db->column($pagesQuery, [$ckey, $ckey])) / $per_page);
        // $this->setPages((int) ceil(array_sum($this->db->column($pagesQuery, $ckey, $ckey) / $per_page)));
        $this->setResults(
            $this->run(
                $query,
                $ckey,
                $ckey,
                ($page * $per_page) - $per_page,
                $per_page
            ),
        );

        return $this;
    }

    public function getTicketsForRound(int $round, int $page = 1, int $per_page = 60): self
    {
        $cols = implode(",\n", $this->columns);
        $joins = implode("\n", $this->joins);
        $where = implode("\n AND ", [...$this->where, 't.round_id = ?']);
        $pagesQuery = sprintf("SELECT count(t.id) FROM ticket t WHERE %s", $where);
        $query = sprintf("SELECT %s FROM ticket t %s \nWHERE %s
      ORDER BY t.timestamp DESC LIMIT ?, ?", $cols, $joins, $where);
        $this->setPages((int) ceil($this->cell($pagesQuery, $round) / $per_page));
        $this->setResults(
            $this->run(
                $query,
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
            $this->run(
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
                    as `replies`
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

    public function getTicketsByServerLastMonth(): array
    {
        $data = $this->run("SELECT count(t.id) AS lastmonth,
        t.server_port
        FROM ticket t
        WHERE `action` = 'Ticket Opened' AND t.recipient IS NULL
        AND YEAR(`timestamp`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(`timestamp`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        GROUP BY t.server_port;");
        $servers = ServerInformationService::getServerInfo();
        foreach ($data as $d) {
            $d->server = ServerInformationService::getServerFromPort($d->server_port, $servers);
        }
        return $data;
    }

    public function getTicketStatsForCkey(string $ckey): array
    {
        return $this->actualRow("SELECT 
        (SELECT count(id) FROM ticket WHERE `action` = 'Ticket Opened' AND sender = ? AND recipient IS NULL) as ahelps,
        (SELECT count(id) FROM ticket WHERE `action` = 'Ticket Opened' AND recipient = ? AND sender IS NOT NULL) as bwoinks", [$ckey, $ckey]);
    }

}
