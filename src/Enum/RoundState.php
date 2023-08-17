<?php

namespace App\Enum;

enum RoundState: string
{
    case PROPER = 'Proper Completion';
    case NUKE = 'Nuked';
    case VOTE = 'Restart Vote';
    case RESTART = 'Restarted by admin';
    case UNSUCCESSFUL = 'No End State';
    case UNDERWAY = 'In progress';

    public function cssClass(): string
    {
        return match($this) {
            RoundState::PROPER => 'success',
            RoundState::NUKE => 'nuke',
            RoundState::VOTE => 'info',
            RoundState::RESTART => 'warning',
            RoundState::UNSUCCESSFUL => 'danger',
            RoundState::UNDERWAY => 'secondary',
        };
    }

    public function icon(): string
    {
        return match($this) {
            RoundState::PROPER => 'fa-solid fa-circle-check',
            RoundState::NUKE => 'fa-solid fa-bomb',
            RoundState::VOTE => 'fa-solid fa-check-to-slot',
            RoundState::RESTART => 'fa-solid fa-power-off',
            RoundState::UNSUCCESSFUL => 'fa-solid fa-triangle-exclamation',
            RoundState::UNDERWAY => 'fa-solid fa-spinner fa-spin'
        };
    }

    public function text(): string
    {
        return match($this) {
            default => $this->value,
            RoundState::UNSUCCESSFUL => 'No End State - Probable Server Crash',
        };
    }
}
