<?php

namespace App\Domain\Player\Service;

use App\Domain\Ban\Data\StandingEnum;
use App\Domain\Ban\Repository\BanRepository;
use DI\Attribute\Inject;

class IsPlayerBannedService
{
    #[Inject]
    private BanRepository $banRepository;

    public function isPlayerBanned($ckey): array
    {
        $standing = [];
        $standing['bans'] = (array) $this->banRepository->getPlayerStanding($ckey)->getResults();
        if (!$standing['bans']) {
            $standing['status'] = StandingEnum::NOT_BANNED;
            return $standing;
        }

        foreach ($standing['bans'] as $b) {
            $b = (array) $b;
            $b['perm'] = (isset($b['expiration_time'])) ? false : true;
        }
        if ($b['perm'] && 'Server' === $b['role']) {
            $standing['status'] = StandingEnum::PERMABANNED;
            return $standing;
        } else {
            $standing['status'] = StandingEnum::ACTIVE_BANS;
        }
        return $standing;
    }

}
