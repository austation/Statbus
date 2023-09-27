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
    public $replace = null;

    private ?string $tweakClass = null;

    public function __construct(
        private int $id,
        private DateTime $dateTime,
        private int $round,
        private string $key,
        private string $type,
        private int $version,
        private string $json,
        private bool $decodeJson = true
    ) {
        $class = '\App\Domain\Stat\Data\Tweaks\\'.$this->getKey();
        if(class_exists($class)) {
            $this->tweakClass = $class;
        }
        $this->setTweaks();
        $this->setData();
    }

    public function setTweaks(): self
    {
        $this->tweaks = StatTweaks::tryFrom($this->getKey());
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

    public function setData(mixed $data = false): self
    {
        //Check for existing tweak plans
        $tweaks = $tweaks = $this->getTweaks();
        if($tweaks) {
            $this->setLabels($tweaks->getLabels());
        }

        //Sometimes we just need to set the data and move on
        if($data) {
            $this->data = $data;
            return $this;
        }

        //Or we just want to ship the JSON directly out
        if(!$this->decodeJson) {
            $this->data = $this->json;
            return $this;
        }

        //TODO: Refactor this so that we can use one method of filter and
        // replace
        //If we have some tweaks to apply, check for a filter first
        if($tweaks) {
            //Stick the filter in a property and apply to the un-decoded json
            if($this->filter = $tweaks->getFilter()) {
                $this->setJson(str_replace($this->filter, '', $this->getJson()));
            }
            if($this->replace = $tweaks->getReplacement()) {
                $this->setJson(str_replace($this->replace['needle'], $this->replace['replace'], $this->getJson()));
            }
        }

        //Now decode ths json string
        $this->data = json_decode($this->getJson(), true);

        //Feedback data is keyed under a `data` item, so we need to get around
        // that
        if(isset($this->data['data'])) {
            $this->data = $this->data['data'];
        }

        //If this is tally data, we can go ahead and sum it up here
        if('tally' === $this->getType()) {
            $this->setTotal($this->tallyData());
            arsort($this->data);
        }

        //If a tweak class exists for this data, we'll run it through this first
        //This is useful for stuff like updating URLs or tweaks we can't do in a
        //filter or in twig on the frontend
        if($this->tweakClass) {
            $this->data = $this->tweakClass::tweakData($this->data, $this->getVersion());
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
