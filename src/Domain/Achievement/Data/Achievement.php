<?php

namespace App\Domain\Achievement\Data;

use DateTime;

class Achievement
{
    public function __construct(
        private string $key,
        private int $value,
        private DateTime $updated,
        private int $version,
        private string $type,
        private string $name,
        private string $desc
    ) {

    }


    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function setDesc(string $desc): self
    {
        $this->desc = $desc;

        return $this;
    }
}
