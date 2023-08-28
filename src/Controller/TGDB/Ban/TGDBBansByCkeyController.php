<?php

namespace App\Controller\TGDB\Ban;

use App\Controller\Controller;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBBansByCkeyController extends Controller
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $bans = $this->banRepository->getBansForCkey($ckey);
        return $this->render('tgdb/bans/player.html.twig', [
            
            'bans' => $bans,
            'player' => $ckey,
            'link' => 'tgdb.ban.view'
        ]);
    }

}
