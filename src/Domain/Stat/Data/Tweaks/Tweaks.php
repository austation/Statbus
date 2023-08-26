<?php

namespace App\Domain\Stat\Data\Tweaks;

use App\Domain\Stat\Data\Stat;

interface Tweaks
{
    public static function tweakData(array $data, int $version = 1): array;
}
