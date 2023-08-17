<?php

namespace App\Domain\Note\Data;

enum SeverityEnum: string
{
    case NONE = 'None';
    case MINOR = 'Minor';
    case MEDIUM = 'Medium';
    case HIGH = 'High';

    public function getCssClass(): string
    {
        return match($this) {
            SeverityEnum::NONE => 'success text-white',
            SeverityEnum::MINOR => 'info',
            SeverityEnum::MEDIUM => 'warning',
            SeverityEnum::HIGH => 'danger'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            SeverityEnum::NONE => 'fa-solid fa-circle-check',
            SeverityEnum::MINOR => 'fa-solid fa-exclamation',
            SeverityEnum::MEDIUM => 'fa-solid fa-triangle-exclamation',
            SeverityEnum::HIGH => 'fa-solid fa-circle-exclamation'
        };
    }

    public function getText(): string
    {
        return match($this) {
            SeverityEnum::NONE => 'No Severity',
            SeverityEnum::MINOR => 'Minor Severity',
            SeverityEnum::MEDIUM => 'Medium Severity',
            SeverityEnum::HIGH => 'High Severity'
        };
    }
    public function getShortText(): string
    {
        return match($this) {
            SeverityEnum::NONE => 'None',
            SeverityEnum::MINOR => 'Minor',
            SeverityEnum::MEDIUM => 'Medium',
            SeverityEnum::HIGH => 'High'
        };
    }

}
