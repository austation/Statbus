<?php

namespace App\Domain\Player\Repository;

use App\Domain\Player\Data\Player;
use App\Domain\Player\Data\PlayerBadge;
use App\Domain\Player\Service\GetPlayerDiscordUsername;
use App\Domain\Player\Service\KeyToCkeyService;
use App\Domain\Jobs\Data\Jobs;
use App\Repository\Repository;
use DI\Attribute\Inject;

class PlayerRepository extends Repository
{
    #[Inject]
    private GetPlayerDiscordUsername $discordUser;

    public ?string $entityClass = Player::class;

    private string $table = 'player p';

    private array $columns = [
        'p.ckey',
        'p.firstseen',
        'p.lastseen',
        'p.firstseen_round_id as firstSeenRound',
        'p.lastseen_round_id as lastSeenRound',
        'p.accountjoindate as accountJoined',
        'a.rank',
        'r.flags',
    ];

    private array $joins = [
        '`admin` a ON a.ckey = p.ckey',
        "admin_ranks r ON SUBSTRING_INDEX(a.rank,' + ',1) = r.rank",
    ];

    private array $where = [
        'p.ckey = ?'
    ];

    public function getPlayerByCkey(string $ckey, bool $fullMonty = false): Player
    {
        if($fullMonty) {
            $this->columns[] = 'count(distinct c.round_id) as rounds, p.ip, p.computerid, COUNT(l.id) as books';
            $this->joins[] = 'library l ON l.ckey = p.ckey';
            $this->joins[] = 'connection_log c ON c.ckey = p.ckey';
        }

        $query = $this->buildQuery($this->table, $this->columns, $this->joins, $this->where, false, false);
        $data = $this->actualRow(
            $query,
            [$ckey]
        );
        $this->setResult($data);
        return $this->getResult();
    }
    public function getPlayerRecentPlaytime(string $ckey): array
    {
        $list = [];
        foreach(Jobs::cases() as $job) {
            if($job->includeInGraph()) {
                $list[] = $job->value;
            }
        }
        $jobs = "('".implode("','", $list)."')";
        $data = $this->run(
            "SELECT sum(t.delta) as `minutes`, t.job FROM role_time_log t
            WHERE t.ckey = ?
            AND t.job in $jobs
            AND t.datetime BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
            GROUP BY t.job
            ORDER BY `minutes` DESC",
            $ckey
        );
        foreach($data as &$d) {
            $job = Jobs::tryFrom($d->job);
            if(!$job) {
                continue;
            }
            $d->minutes = (int) $d->minutes + (rand(1, 3) * 10);
            $d->background = $job->getColor();
        }
        $this->setResults($data, true);
        return $this->getResults();
    }

    public function getDiscordVerificationsForCkey(string $ckey): array
    {
        $data = $this->run("SELECT d.ckey, d.discord_id, d.timestamp, d.valid FROM discord_links d WHERE d.ckey = ?", $ckey);
        foreach($data as &$d) {
            $d = $this->parseTimestamps($d);
            if($d->valid) {
                $d->user = $this->discordUser->getDiscordUser($d->discord_id)->toArray();
            }
        }
        $this->setResults($data, true);
        return $this->getResults();
    }

    public function getKnownAltsForCkey(string $ckey): array
    {
        $this->setResults($this->run("SELECT k.id,
        k.ckey1,
        k.ckey2,
        k.admin_ckey,
        r.rank,
        c1r.rank as ck1_rank,
        c2r.rank as ck2_rank
        FROM known_alts k 
        LEFT JOIN admin r ON k.admin_ckey = r.ckey
        LEFT JOIN admin c1r ON k.ckey1 = c1r.ckey
        LEFT JOIN admin c2r ON k.ckey2 = c2r.ckey
        WHERE ckey1 = ?", $ckey), true);
        $ckeys = [];
        foreach ($this->getResults() as $r) {
            $r->ck1Badge = PlayerBadge::fromRank($r->ckey1, $r->ck1_rank);
            $r->ck2Badge = PlayerBadge::fromRank($r->ckey2, $r->ck2_rank);
            $r->adminBadge = PlayerBadge::fromRank($r->admin_ckey, $r->rank);
            $ckeys[] = $r;
        }
        return $ckeys;
    }

    public function ckeySearch(string $term): array
    {
        $term = KeyToCkeyService::getCkey($term);
        $ckey = $term['ckey'];
        return $this->run(
            "SELECT ckey FROM player WHERE ckey LIKE ?
                  ORDER BY lastseen DESC LIMIT 0, 5",
            '%' . $this->db->escapeLikeValue($ckey) . '%'
        );
    }

    public function getNewPlayers(): array
    {
        $data = (array) $this->run("SELECT player.ckey, player.firstseen, player.lastseen, player.accountjoindate,
        (SELECT GROUP_CONCAT(DISTINCT ckey) FROM connection_log AS dupe WHERE dupe.datetime BETWEEN player.firstseen - INTERVAL 3 DAY AND player.firstseen AND dupe.computerid IN (SELECT DISTINCT connection_log.computerid FROM connection_log WHERE connection_log.ckey = player.ckey) AND dupe.ckey != player.ckey) AS cid_recent_connection_matches,

        (SELECT GROUP_CONCAT(DISTINCT ckey) FROM connection_log AS dupe WHERE dupe.datetime BETWEEN player.firstseen - INTERVAL 3 DAY AND player.firstseen AND dupe.ip IN (select DISTINCT connection_log.ip FROM connection_log WHERE connection_log.ckey = player.ckey) AND dupe.ckey != player.ckey) AS ip_recent_connection_matches,

        (SELECT GROUP_CONCAT(DISTINCT ckey) FROM player AS dupe WHERE dupe.computerid IN (SELECT DISTINCT connection_log.computerid FROM connection_log WHERE connection_log.ckey = player.ckey) AND dupe.ckey != player.ckey) AS cid_last_connection_matches,

        (SELECT GROUP_CONCAT(DISTINCT ckey) FROM player AS dupe WHERE dupe.ip IN (select DISTINCT connection_log.ip FROM connection_log WHERE connection_log.ckey = player.ckey) AND dupe.ckey != player.ckey) AS ip_last_connection_matches

        FROM player
        WHERE player.firstseen > NOW() - INTERVAL 3 DAY
        GROUP BY player.ckey
        ORDER BY player.firstseen DESC;");
        foreach ($data as &$d) {
            $d = (array)$d;
            $d['cid_recent_connection_matches'] = explode(',', $d['cid_recent_connection_matches']);
            $d['ip_recent_connection_matches'] = explode(',', $d['ip_recent_connection_matches']);
            $d['cid_last_connection_matches'] = explode(',', $d['cid_last_connection_matches']);
            $d['ip_last_connection_matches'] = explode(',', $d['ip_last_connection_matches']);
        }
        return $data;
    }
}
