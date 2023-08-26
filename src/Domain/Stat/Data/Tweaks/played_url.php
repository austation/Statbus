<?php

namespace App\Domain\Stat\Data\Tweaks;

use App\Domain\Stat\Data\Tweaks\Tweaks;
use App\Service\YouTubeEmbedConverterService;

class played_url implements Tweaks
{
    public static function tweakData(array $data, int $version = 1): array
    {
        foreach ($data as $k => &$v) {
            foreach ($v as $url => $count) {
                $arr = [];
                if (false !== str_contains($url, 'youtu')) {
                    $arr[] = [
                        'song' => YouTubeEmbedConverterService::getYoutubeEmbedUrl($url),
                        'embed' => true,
                        'count' => $count
                    ];
                } else {
                    $arr = [
                        'song' => $url,
                        'embed' => false,
                        'count' => $count
                    ];
                }
            }
            $data[$k] = $arr;
        }
        return $data;
    }
}
