<?php

namespace App\Domain\Stat\Enum;

enum ThreatLevel: string {
    case WHITE_DWARF = 'White Dwarf';
    case GREEN_STAR = 'Green Star';
    case YELLOW_STAR = 'Yellow Star';
    case ORANGE_STAR = 'Orange Star';
    case RED_STAR = 'Red Star';
    case BLACK_ORBIT = 'Black Orbit';
    case MIDNIGHT_SUN = 'Midnight Sun';

    public function getForeColor(): string {
        return match($this){
            ThreatLevel::WHITE_DWARF, ThreatLevel::YELLOW_STAR, ThreatLevel::ORANGE_STAR  => '#000',
            default => '#FFF'
        };
    }

    public function getBackColor(): string {
        return match($this) {
            ThreatLevel::WHITE_DWARF => '#FFF',
            ThreatLevel::GREEN_STAR => '#146c43',
            ThreatLevel::YELLOW_STAR => '#ffcd39',
            ThreatLevel::ORANGE_STAR => '#fd7e14',
            ThreatLevel::RED_STAR => '#b02a37',
            ThreatLevel::BLACK_ORBIT => '#000',
            ThreatLevel::MIDNIGHT_SUN => '#031633'
        };
    }

    public function getStyle(): string {
        return sprintf("color: %s; background-color: %s", $this->getForeColor(), $this->getBackColor());
    }
}