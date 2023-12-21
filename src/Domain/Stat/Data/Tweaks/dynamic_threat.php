<?php

namespace App\Domain\Stat\Data\Tweaks;

class dynamic_threat implements Tweaks {

    public static function tweakData(array $data, int $version = 1): array 
    {
        $data = $data[1];
        switch(true){ 
            case ((int) $data['threat_level'] == 0):
                $data['name'] = 'White Dwarf';
                break;
            case ((int) $data['threat_level'] < 19):
                $data['name'] = 'Green Star';
                break;
            case ((int) $data['threat_level'] < 39):
                $data['name'] = 'Yellow Star';
                break;
            case ((int) $data['threat_level'] < 65):
                $data['name'] = 'Orange Star';
                break;
            case ((int) $data['threat_level'] < 79):
                $data['name'] = 'Red Star';
                break;
            case ((int) $data['threat_level'] < 99):
                $data['name'] = 'Black Orbit';
                break;
            case ((int) $data['threat_level'] > 100):
                $data['name'] = 'Midnight Sun';
                break;
        }
        return $data;
    }

}