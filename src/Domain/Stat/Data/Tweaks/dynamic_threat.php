<?php

namespace App\Domain\Stat\Data\Tweaks;

use App\Domain\Stat\Enum\ThreatLevel;

class dynamic_threat implements Tweaks {

    public static function tweakData(array $data, int $version = 1): array 
    {
        $data = $data[1];
        switch(true){ 
            case ((int) $data['threat_level'] == 0):
                $data['name'] = ThreatLevel::WHITE_DWARF;
                break;
            case ((int) $data['threat_level'] < 19):
                $data['name'] = ThreatLevel::GREEN_STAR;
                break;
            case ((int) $data['threat_level'] < 39):
                $data['name'] = ThreatLevel::YELLOW_STAR;
                break;
            case ((int) $data['threat_level'] < 65):
                $data['name'] = ThreatLevel::ORANGE_STAR;
                break;
            case ((int) $data['threat_level'] < 79):
                $data['name'] = ThreatLevel::RED_STAR;
                break;
            case ((int) $data['threat_level'] < 99):
                $data['name'] = ThreatLevel::BLACK_ORBIT;
                break;
            case ((int) $data['threat_level'] > 100):
                $data['name'] = ThreatLevel::MIDNIGHT_SUN;
                break;
        }
        return $data;
    }

}