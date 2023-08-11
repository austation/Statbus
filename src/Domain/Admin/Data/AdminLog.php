<?php

namespace App\Domain\Admin\Data;

use App\Domain\Player\Data\PlayerBadge;
use DateTime;

class AdminLog
{
    public ?PlayerBadge $adminBadge = null;
    public ?PlayerBadge $targetBadge = null;

    public function __construct(
        public int $id,
        public DateTime $datetime,
        public ?int $round = null,
        public string $adminckey,
        public $operation = null,
        public ?string $target = null,
        public string $log,
        public ?string $a_rank,
        public ?string $t_rank
    ) {
        $this->setBadges();
        $this->setOperation();
    }

    public function setBadges(): self
    {
        $this->setAdminBadge();
        $this->setTargetBadge();
        return $this;
    }

    public function getAdminBadge(): ?PlayerBadge
    {
        return $this->adminBadge;
    }

    public function setAdminBadge(): self
    {
        if($this->getARank()) {
            $this->adminBadge = PlayerBadge::fromRank($this->getAdminckey(), $this->getARank());
        }

        return $this;
    }

    public function getTargetBadge(): ?PlayerBadge
    {
        return $this->targetBadge;
    }

    public function setTargetBadge(): self
    {
        if($this->t_rank) {
            $this->targetBadge = PlayerBadge::fromRank($this->getTarget(), $this->getTRank());
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDatetime(): DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(DateTime $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getAdminckey(): string
    {
        return $this->adminckey;
    }

    public function setAdminckey(string $adminckey): self
    {
        $this->adminckey = $adminckey;

        return $this;
    }

    public function getOperation(): ?AdminLogOperationEnum
    {
        return $this->operation;
    }

    public function setOperation(): self
    {
        $this->operation = AdminLogOperationEnum::tryFrom($this->operation);

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getLog(): string
    {
        return $this->log;
    }

    public function setLog(string $log): self
    {
        $this->log = $log;

        return $this;
    }

    public function getARank(): ?string
    {
        return $this->a_rank;
    }

    public function setARank(?string $a_rank): self
    {
        $this->a_rank = $a_rank;

        return $this;
    }

    public function getTRank(): ?string
    {
        return $this->t_rank;
    }

    public function setTRank(?string $t_rank): self
    {
        $this->t_rank = $t_rank;

        return $this;
    }
}
