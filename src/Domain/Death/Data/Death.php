<?php

namespace App\Domain\Death\Data;

use App\Domain\Job\Data\JobBadge;
use App\Domain\Player\Data\PlayerBadge;
use App\Domain\Server\Data\Server;
use App\Enum\Jobs;
use DateTime;

class Death
{
    private ?PlayerBadge $playerBadge = null;
    private ?PlayerBadge $attackerBadge = null;
    private ?JobBadge $jobBadge = null;

    public function __construct(
        private int $id,
        private string $location,
        private int $x,
        private int $y,
        private int $z,
        private string $map,
        private int $serverIp,
        private int $serverPort,
        private int $round,
        private DateTime $timestamp,
        private string $job,
        private ?string $role = null,
        private string $name,
        private string $ckey,
        private ?string $attacker = null,
        private ?string $attackerCkey = null,
        private int $brute,
        private int $brain,
        private int $fire,
        private int $oxy,
        private int $tox,
        private int $clone,
        private int $stam,
        private ?string $last_words = null,
        private ?bool $suicide = null,
        private ?string $playerRank = null,
        private ?string $attackerRank = null,
        private Server $server
    ) {
        $this->setBadges();
    }

    private function setBadges(): self
    {
        $this->setPlayerBadge();
        $this->setAttackerBadge();
        $this->setJobBadge();
        return $this;
    }

    private function setPlayerBadge(): self
    {
        $this->playerBadge = PlayerBadge::fromRank($this->ckey, $this->playerRank);
        return $this;
    }

    private function setAttackerBadge(): self
    {
        if($this->attackerCkey) {
            $this->attackerBadge = PlayerBadge::fromRank($this->attackerCkey, $this->attackerRank);
        }
        return $this;
    }

    private function setJobBadge(): self
    {
        if($this->job) {
            $this->jobBadge = new JobBadge(Jobs::tryFrom($this->job), $this->job);
        }
        return $this;
    }

    public function getPlayerBadge(): ?PlayerBadge
    {
        return $this->playerBadge;
    }

    public function getJobBadge(): ?JobBadge
    {
        return $this->jobBadge;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function getDamage(): array
    {
        return [
            'brute' => $this->brute,
            'brain' => $this->brain,
            'fire' => $this->fire,
            'oxygen' => $this->oxy,
            'toxin' => $this->tox,
            'clone' => $this->clone,
            'stamina' => $this->stam
        ];
    }
    public function getCause(): array
    {
        $max = array_search(max((array) $this->getDamage()), (array) $this->getDamage());
        $cause = [];
        switch ($max) {
            default:
                $cause['cause']      = "Natural Causes";
                $cause['last_line']  = "as they were beaten to death";
                break;
            case 'brute':
                $cause['cause']      = "Blunt-Force Trauma";
                $cause['last_line']  = "as they were beaten to death";
                break;

            case 'brain':
                $cause['cause']      = "Brain Damage";
                $cause['last_line']  = "slurred out as they gave up on life";
                break;

            case 'fire':
                $cause['cause']      = "Severe Burns";
                $cause['last_line']  = "screamed in agony";
                break;

            case 'oxygen':
                $cause['cause']      = "Suffocation";
                $cause['last_line']  = "with their dying breath";
                break;

            case 'toxin':
                $cause['cause']      = "Poisoning";
                $cause['last_line']  = "twitching as toxins coursed through their system";
                break;

            case 'clone':
                $cause['cause']      = "Poor Cloning Technique";
                $cause['last_line']  = "scrawled into the floor where they died";
                break;

            case 'stamina':
                $cause['cause']      = "Exhaustion";
                $cause['last_line']  = "whispered in their final moments";
                break;
        }
        return $cause;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function getCoords(): string
    {
        return sprintf("(%s, %s, %s)", $this->x, $this->y, $this->z);
    }

    public function getMap(): string
    {
        return $this->map;
    }

    public function getAttacker(): ?string
    {
        return $this->attacker;
    }

    public function getAttackerBadge(): ?PlayerBadge
    {
        return $this->attackerBadge;
    }
}
