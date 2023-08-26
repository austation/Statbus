<?php

namespace App\Domain\Ticket\Data;

use App\Domain\Player\Data\PlayerBadge;
use App\Domain\Server\Data\Server;
use App\Enum\TicketActions;
use DateTime;

class Ticket
{
    public ?PlayerBadge $senderBadge = null;
    public ?PlayerBadge $recipientBadge = null;
    public bool $bwoink = false;

    public function __construct(
        public int $id,
        public ?int $serverIp,
        public ?int $port,
        public ?int $round,
        public ?int $ticket,
        public $action = 'Reply',
        public ?string $message,
        public ?DateTime $timestamp,
        public ?string $r_ckey,
        public ?string $s_ckey,
        public ?string $r_rank,
        public ?string $s_rank,
        public $status,
        public ?int $replies,
        public ?bool $urgent = false,
        public ?Server $server = null
    ) {
        $this->setBadges();
        $this->setAction();
        $this->setIsBwoink();
        $this->setStatus();
        $this->setMessage($this->getMessage());
    }

    private function setBadges(): self
    {
        $this->setSenderBadge();
        $this->setRecipientBadge();
        return $this;
    }

    public function getSenderBadge(): ?PlayerBadge
    {
        return $this->senderBadge;
    }

    public function setSenderBadge(): self
    {
        if($this->getSCkey()) {
            $this->senderBadge = PlayerBadge::fromRank($this->getSCkey(), $this->getSRank());
        }
        return $this;
    }

    public function getRecipientBadge(): ?PlayerBadge
    {
        return $this->recipientBadge;
    }

    public function setRecipientBadge(): self
    {
        if($this->getRCkey()) {
            $this->recipientBadge = PlayerBadge::fromRank($this->getRCkey(), $this->getRRank());
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

    public function getServerIp(): ?int
    {
        return $this->serverIp;
    }

    public function setServerIp(?int $serverIp): self
    {
        $this->serverIp = $serverIp;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(?int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getTicket(): ?int
    {
        return $this->ticket;
    }

    public function setTicket(?int $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getAction(): ?TicketActions
    {
        return $this->action;
    }

    public function setAction(): self
    {
        $this->action = TicketActions::tryFrom($this->action);

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = nl2br($message);

        return $this;
    }

    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(?DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getRCkey(): ?string
    {
        return $this->r_ckey;
    }

    public function setRCkey(?string $r_ckey): self
    {
        $this->r_ckey = $r_ckey;

        return $this;
    }

    public function getSCkey(): ?string
    {
        return $this->s_ckey;
    }

    public function setSCkey(?string $s_ckey): self
    {
        $this->s_ckey = $s_ckey;

        return $this;
    }

    public function getRRank(): ?string
    {
        return $this->r_rank;
    }

    public function setRRank(?string $r_rank): self
    {
        $this->r_rank = $r_rank;

        return $this;
    }

    public function getSRank(): ?string
    {
        return $this->s_rank;
    }

    public function setSRank(?string $s_rank): self
    {
        $this->s_rank = $s_rank;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(): self
    {
        $this->status = TicketActions::tryFrom($this->status);

        return $this;
    }

    public function getReplies(): ?int
    {
        return $this->replies;
    }

    public function setReplies(?int $replies): self
    {
        $this->replies = $replies;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function setIsBwoink(): self
    {
        if($this->getRCkey() && $this->getSCkey() && $this->action === TicketActions::OPENED) {
            $this->bwoink = true;
        }
        return $this;
    }

    public function getBwoink(): bool
    {
        return $this->bwoink;
    }

    public function isUrgent(): bool
    {
        return $this->urgent;
    }

}
