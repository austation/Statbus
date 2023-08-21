<?php

namespace App\Controller\TGDB\Player;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Player\Service\IsPlayerBannedService;
use App\Domain\Player\Service\KeyToCkeyService;
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
        $ckey = KeyToCkeyService::getCkey($ckey);
        $player = $this->playerRepository->getPlayerByCkey($ckey, true);
        $standing = $this->bannedService->isPlayerBanned($ckey);

        if(isset($_GET['format']) && 'popover' === $_GET['format']) {
            return $this->render('tgdb/player/popover.html.twig', [
                'player' => $player,
                'standing' => $standing,
            ]);
        } else {
            $playTime = $this->playerRepository->getPlayerRecentPlaytime($ckey);
        }
        return $this->render('tgdb/player/single.html.twig', [
            'player' => $player,
            'playtime' => $playTime,
            'standing' => $standing,
            'narrow' => true,
            'perms' => PermissionsFlags::getArray(),
            'alts' => $this->playerRepository->getKnownAltsForCkey($ckey)
        ]);
    }

}
