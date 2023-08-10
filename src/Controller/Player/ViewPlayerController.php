<?php

namespace App\Controller\Player;

use App\Controller\Controller;
use App\Domain\Achievement\Repository\AchievementRepository;
use App\Domain\Player\Repository\PlayerRepository;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class ViewPlayerController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    #[Inject]
    private AchievementRepository $achievementRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $player = $this->playerRepository->getPlayerByCkey($ckey);
        $playTime = $this->playerRepository->getPlayerRecentPlaytime($ckey);
        $achievements = $this->achievementRepository->getAchievementsForCkey($ckey);
        if(isset($_GET['format']) && 'popover' === $_GET['format']) {
            return $this->render('player/popover.html.twig', [
                'player' => $player,
            ]);
        }
        return $this->render('player/single.html.twig', [
            'player' => $player,
            'playtime' => $playTime,
            'narrow' => true,
            'perms' => PermissionsFlags::getArray(),
            'achievements' => $achievements
        ]);
    }

}
