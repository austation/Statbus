<?php

namespace App\Domain\Library\Data;

enum ActionEnum: string
{
    case REPORTED = 'reported';
    case DELETED = 'deleted';
    case UNDELETED = 'undeleted';

    public function getIcon(): string
    {
        return match($this) {
            ActionEnum::REPORTED => 'fa-solid fa-flag',
            ActionEnum::DELETED => 'fa-solid fa-trash',
            ActionEnum::UNDELETED => 'fa-solid fa-trash-can-arrow-up'
        };
    }

    public function getCssClass(): string
    {
        return match($this) {
            ActionEnum::REPORTED => 'warning',
            ActionEnum::DELETED => 'danger',
            ActionEnum::UNDELETED => 'info'
        };
    }

}
