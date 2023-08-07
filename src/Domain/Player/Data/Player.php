<?php

namespace App\Domain\Player\Data;

use App\Enum\PermissionsFlags;
use DateTime;

class Player
{
    private ?PlayerBadge $playerBadge = null;

    private array $roles = [];

    public function __construct(
        private string $ckey,
        private DateTime $firstSeen,
        private DateTime $lastSeen,
        private int $firstSeenRound,
        private int $lastSeenRound,
        private DateTime $accountJoined,
        private ?string $rank = null,
        private ?int $flags = null,
        private ?int $ip = null,
        private ?int $cid = null,
    ) {
        $this->setPlayerBadge();
    }

    public function getCkey(): string
    {
        return $this->ckey;
    }

    public function setCkey(string $ckey): self
    {
        $this->ckey = $ckey;

        return $this;
    }

    public function getFirstSeen(): DateTime
    {
        return $this->firstSeen;
    }

    public function setFirstSeen(DateTime $firstSeen): self
    {
        $this->firstSeen = $firstSeen;

        return $this;
    }

    public function getLastSeen(): DateTime
    {
        return $this->lastSeen;
    }

    public function setLastSeen(DateTime $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getFirstSeenRound(): int
    {
        return $this->firstSeenRound;
    }

    public function setFirstSeenRound(int $firstSeenRound): self
    {
        $this->firstSeenRound = $firstSeenRound;

        return $this;
    }

    public function getLastSeenRound(): int
    {
        return $this->lastSeenRound;
    }

    public function setLastSeenRound(int $lastSeenRound): self
    {
        $this->lastSeenRound = $lastSeenRound;

        return $this;
    }

    public function getAccountJoined(): DateTime
    {
        return $this->accountJoined;
    }

    public function setAccountJoined(DateTime $accountJoined): self
    {
        $this->accountJoined = $accountJoined;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function setFlags(?int $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    public function getPlayerBadge(): ?PlayerBadge
    {
        return $this->playerBadge;
    }

    public function setPlayerBadge(): self
    {
        $this->playerBadge = PlayerBadge::fromRank($this->getCkey(), $this->getRank());
        return $this;
    }

}
