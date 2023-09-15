<?php

namespace App\Controller\TGDB\Player;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Player\Service\IsPlayerBannedService;
use App\Domain\Player\Service\KeyToCkeyService;
use App\Domain\Ticket\Repository\TicketRepository;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBPlayerViewController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    #[Inject]
    private IsPlayerBannedService $bannedService;

    #[Inject]
    private AdminLogRepository $adminLog;

    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $ckeyResult = KeyToCkeyService::getCkey($ckey);
        if(0 !== $ckeyResult['replacements']) {
            // $this->addSuccessMessage("Redirecting to player ckey");
            return $this->routeRedirect('tgdb.player', ['ckey' => $ckeyResult['ckey']]);
        }
        $ckey = $ckeyResult['ckey'];
        $player = $this->playerRepository->getPlayerByCkey($ckey, true);
        $standing = $this->bannedService->isPlayerBanned($ckey);
        $logs = $this->adminLog->getAdminLogsForCkey($ckey);
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
            'logs' => $logs,
            'perms' => PermissionsFlags::getArray(),
            'alts' => $this->playerRepository->getKnownAltsForCkey($ckey),
            'ticketStats' => $this->ticketRepository->getTicketStatsForCkey($ckey)
        ]);
    }

}
