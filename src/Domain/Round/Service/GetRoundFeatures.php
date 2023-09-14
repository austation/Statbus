<?php

namespace App\Domain\Round\Service;

use DateTime;

class GetRoundFeatures
{
    /**
     * supportsRoundEndData
     *
     * Only query for round_end_data.* log files for rounds after this date.
     *
     * @link https://github.com/tgstation/tgstation/pull/34465
     *
     * @param DateTime $date
     * @return boolean
     */
    public static function supportsRoundEndData(DateTime $date): bool
    {
        if($date > new DateTime('2018-01-18')) {
            return true;
        }
        return false;
    }

}
