<?php

namespace App\Domain\Round\Data;

enum TimelineKeys: string
{
    case ROUND_START = 'round_start';
    case ROUND_END = 'round_end';
    case EXPLOSION = 'explosion';
    case DEATH = 'death';
    case TCOMMS = 'tcomms';
    case MANIFEST = 'manifest';
    case DYNAMIC = 'dynamic';
    case SHUTTLE = 'shu';

    public function getIcon(): string
    {
        return match($this) {
            TimelineKeys::ROUND_START => 'fa-solid fa-play',
            TimelineKeys::ROUND_END => 'fa-solid fa-rocket',
            TimelineKeys::EXPLOSION => 'fa-solid fa-bomb',
            TimelineKeys::DEATH => 'fa-solid fa-skull',
            TimelineKeys::TCOMMS => 'fa-solid fa-walkie-talkie',
            TimelineKeys::MANIFEST => 'fa-solid fa-briefcase',
            TimelineKeys::DYNAMIC => 'fa-solid fa-dice',
            TimelineKeys::SHUTTLE => 'fa-solid fa-shuttle-space'
        };
    }

    public function getColor(): string
    {
        return match($this) {
            TimelineKeys::ROUND_START => 'info',
            TimelineKeys::ROUND_END => 'info',
            TimelineKeys::EXPLOSION => 'danger',
            TimelineKeys::DEATH => 'dark',
            TimelineKeys::TCOMMS => 'primary',
            TimelineKeys::MANIFEST => 'primary',
            TimelineKeys::DYNAMIC => 'primary',
            TimelineKeys::SHUTTLE => 'primary',
        };
    }

    public function getName(): string
    {
        return match($this) {
            TimelineKeys::ROUND_START => 'Round Start',
            TimelineKeys::ROUND_END => 'Round End',
            TimelineKeys::EXPLOSION => 'Explosion',
            TimelineKeys::DEATH => 'Death',
            TimelineKeys::TCOMMS => 'Telecomms',
            TimelineKeys::MANIFEST => 'Manifest',
            TimelineKeys::DYNAMIC => 'Dynamic',
            TimelineKeys::SHUTTLE => 'Shuttle'
        };
    }

}
