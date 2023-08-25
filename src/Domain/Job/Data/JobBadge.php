<?php

namespace App\Domain\Job\Data;

use App\Enum\Jobs;

class JobBadge
{
    public string $name = "A Job!";
    public string $backColor = "#CCC";
    public string $foreColor = "#FFF";
    public ?string $style = null;

    public function __construct(
        private ?Jobs $job,
        private ?string $role = null
    ) {
        if($job) {
            $this->name = $job->value;
            $this->backColor = $job->getColor();
            $this->foreColor = $job->getForeColor();
        } else {
            $this->name = $role;
            $this->backColor = "#CCC";
            $this->foreColor = "#000";
        }
        $this->setStyle();
    }

    private function setStyle(): self
    {
        $this->style = sprintf(
            "background: %s; color: %s;",
            $this->backColor,
            $this->foreColor
        );
        return $this;
    }

}
