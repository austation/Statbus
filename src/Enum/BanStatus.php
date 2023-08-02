<?php

namespace App\Enum;

enum BanStatus: string
{
    case EXPIRED = 'Expired';
    case LIFTED = 'Lifted';
    case ACTIVE = 'Active';
    case PERMANENT = 'Permanent';

    public function getCssClass(): string
    {
        return match($this) {
            BanStatus::EXPIRED => 'success',
            BanStatus::LIFTED => 'info',
            BanStatus::ACTIVE => 'danger',
            BanStatus::PERMANENT => 'perma'
        };
    }

}
