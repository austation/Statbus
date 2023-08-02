<?php

namespace App\Domain\Server\Data;

class Server
{
    private ?string $name;
    private ?string $address;
    private ?int $port;
    private ?string $identifier;

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

    public static function fromArray(array $data): self
    {
        $server = new self();
        $server->setName($data['servername']);
        $server->setPort($data['port']);
        $server->setIdentifier($data['identifier']);
        $server->setAddress($data['address']);
        return $server;
    }
}
