<?php

namespace App\Domain\Stat\Data;

use DateTime;

class Stat
{
    public $data;
    private array $labels = [];
    private ?StatTweaks $tweaks = null;
    private ?int $total = null;

    public array $filter = [];

    public function __construct(
        private int $id,
        private DateTime $dateTime,
        private int $round,
        private string $key,
        private string $type,
        private int $version,
        private string $json
    ) {
        $this->setTweaks();
        $this->setData();
    }

    public function setTweaks(): self
    {
        $this->tweaks = StatTweaks::tryFrom($this->getKey());
        if($this->tweaks) {
            $this->setLabels($this->tweaks->getLabels());
        }
        return $this;
    }

    public function getTweaks(): ?StatTweaks
    {
        return $this->tweaks;
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }

    public function getLabels(): array
    {
        return $this->labels;
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

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTime $dateTime): self
    {
        $this->dateTime = $dateTime;

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

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

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

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getJson(): string
    {
        return $this->json;
    }

    public function setJson(string $json): self
    {
        $this->json = $json;

        return $this;
    }

    public function setData(): self
    {
        if($tweaks = $this->getTweaks()) {
            if($this->filter = $tweaks->getFilter()) {
                $this->setJson(str_replace($this->filter, '', $this->getJson()));
            }
        }
        $this->data = json_decode($this->getJson(), true)['data'];
        if('tally' === $this->getType()) {
            $this->setTotal($this->tallyData());
            arsort($this->data);
        }
        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    private function tallyData(): int
    {
        return array_sum($this->data);
    }

    private function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }
}
