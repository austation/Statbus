<?php

namespace App\Domain\Player\Service;

class KeyToCkeyService
{
    /**
     * getCkey
     *
     * Normalizes a byond key into a ckey via regex replacement
     *
     * @param string $key
     * @return string
     */
    public static function getCkey(string $key): array
    {
        $r = 0;
        $ckey = strtolower(preg_replace('/[^a-zA-Z0-9@]/', '', $key, -1, $r));
        return [
            'ckey' => $ckey,
            'replacements' => $r
        ];
    }

}
