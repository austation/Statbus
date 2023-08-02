<?php

namespace App\Enum;

use Symfony\Component\Yaml\Yaml;

class AdminRanks
{
    public static function getRankInfo($rank): array
    {
        $ranks = Yaml::parseFile(__DIR__.'/../../assets/ranks.json');
        if(isset($ranks[$rank])) {
            return $ranks[$rank];
        } else {
            return [
                'backColor' => '#ccc',
                'foreColor' => '#000',
                'icon' => 'user'
            ];
        }
    }



}
