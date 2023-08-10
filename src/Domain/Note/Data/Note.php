<?php

namespace App\Domain\Note\Data;

use App\Domain\Player\Data\PlayerBadge;
use App\Domain\Server\Data\Server;
use App\Domain\Note\Data\SeverityEnum;
use DateTime;

class Note
{
    private PlayerBadge $targetBadge;
    private PlayerBadge $adminBadge;
    private PlayerBadge $editorBadge;

    public function __construct(
        private int $id,
        private $type,
        private string $ckey,
        private string $admin,
        private string $text,
        private DateTime $timestamp,
        private int $serverPort,
        private int $serverAddress,
        private int $round,
        private bool $secret,
        private ?DateTime $expiration = null,
        private $severity,
        private ?int $playtime = null,
        private ?string $editor = null,
        private $edits = null,
        private bool $deleted = false,
        private ?string $deleter = null,
        private ?string $targetRank = null,
        private ?string $adminRank = null,
        private ?string $editorRank = null,
        private ?Server $server = null
    ) {
        $this->setBadges();
        $this->setSeverity();
        $this->setType();
        $this->setEdits();
    }

    private function setBadges(): self
    {
        $this->setTargetBadge();
        $this->setAdminBadge();
        $this->setEditorBadge();
        return $this;
    }


    public function getTargetBadge(): PlayerBadge
    {
        return $this->targetBadge;
    }

    public function setTargetBadge(): self
    {
        $this->targetBadge = PlayerBadge::fromRank($this->getCkey(), $this->getTargetRank());
        return $this;
    }

    public function getAdminBadge(): PlayerBadge
    {
        return $this->adminBadge;
    }

    public function setAdminBadge(): self
    {

        $this->adminBadge = PlayerBadge::fromRank($this->getAdmin(), $this->getAdminRank());

        return $this;
    }

    public function getEditorBadge(): PlayerBadge
    {
        return $this->editorBadge;
    }

    public function setEditorBadge(): self
    {
        if($this->getEditor()) {
            $this->editorBadge = PlayerBadge::fromRank($this->getEditor(), $this->getEditorRank());
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

    public function getType(): ?TypeEnum
    {
        return $this->type;
    }

    public function setType(): self
    {
        $this->type = TypeEnum::tryFrom($this->type);
        return $this;
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

    public function getAdmin(): string
    {
        return $this->admin;
    }

    public function setAdmin(string $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getServerPort(): int
    {
        return $this->serverPort;
    }

    public function setServerPort(int $serverPort): self
    {
        $this->serverPort = $serverPort;

        return $this;
    }

    public function getServerAddress(): int
    {
        return $this->serverAddress;
    }

    public function setServerAddress(int $serverAddress): self
    {
        $this->serverAddress = $serverAddress;

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

    public function isSecret(): bool
    {
        return $this->secret;
    }

    public function setSecret(bool $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getExpiration(): ?DateTime
    {
        return $this->expiration;
    }

    public function setExpiration(?DateTime $expiration): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getSeverity(): ?SeverityEnum
    {
        if(!$this->severity instanceof SeverityEnum) {
            return SeverityEnum::NONE;
        }
        return $this->severity;
    }

    public function setSeverity(): self
    {
        $this->severity = SeverityEnum::tryFrom(ucfirst($this->severity));
        return $this;
    }

    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    public function setPlaytime(?int $playtime): self
    {
        $this->playtime = $playtime;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(?string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getEdits(): ?array
    {
        return $this->edits;
    }

    public function setEdits(): self
    {
        if($this->edits) {
            $this->edits = explode("<hr />", $this->edits);
            array_pop($this->edits);
        }

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeleter(): ?string
    {
        return $this->deleter;
    }

    public function setDeleter(?string $deleter): self
    {
        $this->deleter = $deleter;

        return $this;
    }

    public function getTargetRank(): ?string
    {
        return $this->targetRank;
    }

    public function setTargetRank(?string $targetRank): self
    {
        $this->targetRank = $targetRank;

        return $this;
    }

    public function getAdminRank(): ?string
    {
        return $this->adminRank;
    }

    public function setAdminRank(?string $adminRank): self
    {
        $this->adminRank = $adminRank;

        return $this;
    }

    public function getEditorRank(): ?string
    {
        return $this->editorRank;
    }

    public function setEditorRank(?string $editorRank): self
    {
        $this->editorRank = $editorRank;

        return $this;
    }

    public function getServer(): Server
    {
        return $this->server;
    }
}
