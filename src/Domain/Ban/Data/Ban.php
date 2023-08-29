<?php

namespace App\Domain\Ban\Data;

use App\Domain\Player\Data\PlayerBadge;
use App\Domain\Server\Data\Server;
use App\Enum\BanStatus;
use DateTime;

class Ban
{
    public bool $roleBans = false;

    private ?PlayerBadge $playerBadge = null;
    private ?PlayerBadge $adminBadge = null;
    private ?PlayerBadge $unbannerBadge = null;

    private BanStatus $status = BanStatus::ACTIVE;

    public static function fromDb(object $row)
    {
        return new self(
            ...get_object_vars($row)
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }

    public function __construct(
        public int $id = 0,
        public int $round = 0,
        public ?DateTime $bantime = null,
        public ?DateTime $expiration = null,
        public string $ckey = '',
        public ?string $c_rank = 'Player',
        public $role = null,
        public string $a_ckey = '',
        public ?string $a_rank = 'Player',
        public int $server_ip = 0,
        public int $server_port = 0,
        public string $reason = '',
        public $unbanned_ckey = null,
        public ?DateTime $unbanned_datetime = null,
        public $u_rank = '',
        public int $minutes = 0,
        public int $active = 0,
        public $round_time = null,
        public $edits = '',
        public $banIds = null,
        public ?Server $server = null
    ) {
        $this->roleBans();
        $this->splitEdits();
        $this->setPlayerBadge();
        $this->setAdminBadge();
        $this->setUnbannerBadge();
        $this->setStatus();
        // $this->setServerInfo();
    }

    public function getIp()
    {
        return $this->server_ip;
    }

    public function getPort()
    {
        return $this->server_port;
    }

    public function setServer(object $server)
    {
        $this->server = $server;
    }
    private function roleBans()
    {
        if (str_contains($this->role, ', ')) {
            $this->roleBans = true;
            $this->role = explode(', ', $this->role);
        } else {
            $role = $this->role;
            $this->role = [];
            $this->role[] = $role;
        }
        $this->role = array_unique($this->role);
    }
    private function splitEdits()
    {
        if ($this->edits) {
            $this->edits = explode("<br>to<br>", $this->edits);
        }
    }

    private function setAdminBadge(): self
    {
        if(!$this->a_rank) {
            $this->a_rank = 'Player';
        }
        $this->adminBadge = PlayerBadge::fromRank($this->a_ckey, $this->a_rank);
        return $this;
    }

    public function getAdminBadge(): ?PlayerBadge
    {
        return $this->adminBadge;
    }

    private function setPlayerBadge(): self
    {
        if(!$this->c_rank) {
            $this->c_rank = 'Player';
        }
        $this->playerBadge = PlayerBadge::fromRank($this->ckey, $this->c_rank);
        return $this;
    }

    public function getPlayerBadge(): ?PlayerBadge
    {
        return $this->playerBadge;
    }

    private function setUnbannerBadge(): self
    {
        if(!$this->unbanned_ckey) {
            $this->unbannerBadge = null;
            return $this;
        }
        if(!$this->u_rank) {
            $this->u_rank = 'Player';
        }
        $this->unbannerBadge = PlayerBadge::fromRank($this->unbanned_ckey, $this->u_rank);
        return $this;
    }

    public function getUnbannerBadge(): ?PlayerBadge
    {
        return $this->unbannerBadge;
    }

    public function setStatus(): self
    {
        if($this->unbanned_ckey) {
            $this->status = BanStatus::LIFTED;
            return $this;
        }
        if(!$this->expiration) {
            $this->status = BanStatus::PERMANENT;
            return $this;
        }
        if($this->expiration > new DateTime()) {
            $this->status = BanStatus::ACTIVE;
            return $this;
        } else {
            $this->status = BanStatus::EXPIRED;
            return $this;
        }
    }

    public function getStatus(): BanStatus
    {
        return $this->status;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function getCkey(): string
    {
        return $this->ckey;
    }


}
