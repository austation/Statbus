<?php

namespace App\Controller\Player;

use App\Controller\Controller;
use App\Domain\Achievement\Repository\AchievementRepository;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Player\Service\KeyToCkeyService;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class ViewPlayerController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    #[Inject]
    private AchievementRepository $achievementRepository;

    #[Inject]
    private AdminLogRepository $adminLog;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        //TODO: Make this into a service
        //TODO: Redirect to actual ckey url if non-ckey given
        $ckeyResult = KeyToCkeyService::getCkey($ckey);
        if(0 !== $ckeyResult['replacements']) {
            // $this->addSuccessMessage("Redirecting to player ckey");
            return $this->routeRedirect('player', ['ckey' => $ckeyResult['ckey']]);
        }
        $player = $this->playerRepository->getPlayerByCkey($ckey);
        if(isset($_GET['format']) && 'popover' === $_GET['format']) {
            return $this->render('player/popover.html.twig', [
                'player' => $player,
            ]);
        }
        $playTime = $this->playerRepository->getPlayerRecentPlaytime($ckey);
        $achievements = $this->achievementRepository->getAchievementsForCkey($ckey);
        $logs = $this->adminLog->getAdminLogsForCkey($ckey);
        return $this->render('player/single.html.twig', [
            'player' => $player,
            'playtime' => $playTime,

            'perms' => PermissionsFlags::getArray(),
            'achievements' => $achievements,
            'logs' => $logs
        ]);
    }

}
