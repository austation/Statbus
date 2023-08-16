<?php

namespace App\Domain\Stat\Data;

enum StatTweaks: string
{
    case ADMIN_TOGGLE = 'admin_toggle';
    case ADMIN_SECRETS_FUN_USED = 'admin_secrets_fun_used';
    case ADMIN_VERB = 'admin_verb';
    case AHELP_STATS = 'ahelp_stats';
    case CELL_USED = 'cell_used';
    case SURGERIES_COMPLETED = 'surgeries_completed';
    case CHEMICAL_REACTION = 'chemical_reaction';
    case TRAUMAS = 'traumas';
    case RADIO_USAGE = 'radio_usage';

    public function getLabels(): array
    {
        return match($this) {
            default => [],
            StatTweaks::ADMIN_TOGGLE => [
                'key' => 'Feature',
                'value' => 'Count',
                'subvalue' => 'State'
            ],
            StatTweaks::ADMIN_SECRETS_FUN_USED => [
                'key' => 'Button Pressed',
                'value' => 'Times Pressed',
                'subvalue' => 'Conditions'
            ],
            StatTweaks::ADMIN_VERB => [
                'key' => 'Verb',
                'value' => 'Times Used',
                'total' => 'Total Verbs Used'
            ],
            StatTweaks::AHELP_STATS => [
                'key' => 'Status',
                'value' => 'Count',
                'total' => 'Total Tickets'
            ],
            StatTweaks::CELL_USED => [
                'key' => 'Cell',
                'value' => 'Number Used',
                'total' => 'Total Cells Used'
            ],
            StatTweaks::CHEMICAL_REACTION => [
                'key' => 'Reaction Attempted',
                'value' => 'Number of Attempts',
                'total' => 'Total Attempts'
            ],
            StatTweaks::RADIO_USAGE => [
                'key' => 'Channel',
                'value' => 'Number of Messages',
                'total' => 'Total Messages Transmitted'
            ],
        };
    }

    public function getFilter(): array
    {
        return match($this) {
            default => [],
            StatTweaks::SURGERIES_COMPLETED => ['/datum/surgery/'],
            StatTweaks::CHEMICAL_REACTION => ['/datum/chemical_reaction/'],
            StatTweaks::TRAUMAS => ['/datum/brain_trauma/']
        };
    }

}
