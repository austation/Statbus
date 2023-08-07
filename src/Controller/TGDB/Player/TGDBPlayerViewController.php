<?php

namespace App\Controller\TGDB\Player;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Player\Service\IsPlayerBannedService;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBPlayerViewController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;
    #[Inject]
    private IsPlayerBannedService $bannedService;

    public function action(): ResponseInterface
    {


        $ckey = $this->getArg('ckey');
        $player = $this->playerRepository->getPlayerByCkey($ckey, true);
        $playTime = $this->playerRepository->getPlayerRecentPlaytime($ckey);
        $standing = $this->bannedService->isPlayerBanned($ckey);
        return $this->render('tgdb/player/single.html.twig', [
            'player' => $player,
            'playtime' => $playTime,
            'standing' => $standing,
            'narrow' => true,
            'perms' => PermissionsFlags::getArray(),
        ]);
    }

}
