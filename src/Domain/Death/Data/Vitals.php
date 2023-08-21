<?php

namespace App\Domain\Death\Data;

enum Vitals: string
{
    case BRUTE   = 'brute';
    case BRAIN   = 'brain';
    case FIRE    = 'fire';
    case OXYGEN  = 'oxygen';
    case TOX     = 'toxin';
    case CLONE   = 'clone';
    case STAMINA = 'stamina';

    public function getColor(): string
    {
        return match($this) {
            Vitals::BRUTE   => "#fb264b",
            Vitals::BRAIN   => "#5995ba",
            Vitals::FIRE    => "#e0a003",
            Vitals::OXYGEN  => "#689bc3",
            Vitals::TOX     => "#61af25",
            Vitals::CLONE   => "#ab63d8",
            Vitals::STAMINA => "#0e22aa",
        };
    }

    public function getTitle(): string
    {
        return match($this) {
            Vitals::BRUTE   => "Brute Damage",
            Vitals::BRAIN   => "Brain Damage",
            Vitals::FIRE    => "Burn Damage",
            Vitals::OXYGEN  => "Oxygen Loss",
            Vitals::TOX     => "Toxin Damage",
            Vitals::CLONE   => "Clone Damage",
            Vitals::STAMINA => "Stamina Loss",
        };
    }
}
