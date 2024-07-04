<?php

namespace App\Domain\Server\Data;

class Server
{
    public ?string $name;
    public ?string $address;
    public ?int $port;
    public ?string $identifier;
    public ?string $gameLink;
    public ?string $public_logs;
    public ?string $raw_logs;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getGameServerAddress(): string
    {
        return sprintf("byond://%s:%s", $this->getAddress(), $this->getPort());
    }

    private function setGameServerAddress(): self
    {
        $this->gameLink = $this->getGameServerAddress();
        return $this;
    }

    public function getRawLogs(): ?string
    {
        return $this->raw_logs;
    }

    public function setRawLogs(?string $raw_logs): self
    {
        $this->raw_logs = str_replace('.download', '.org', $raw_logs);

        return $this;
    }

    public function getPublicLogs(): ?string
    {
        return $this->public_logs;
    }

    public function setPublicLogs(?string $public_logs): self
    {
        $this->public_logs = str_replace('.download', '.org', $public_logs);

        return $this;
    }

    public static function fromArray(array $data): self
    {
        $server = new self();
        $server->setName($data['servername']);
        $server->setPort($data['port']);
        $server->setIdentifier($data['identifier'] ?? $data['servername']);
        $server->setAddress($data['address']);
        $server->setGameServerAddress();
        $server->setPublicLogs($data['public_logs_url']);
        $server->setRawLogs($data['raw_logs_url']);
        return $server;
    }
}
