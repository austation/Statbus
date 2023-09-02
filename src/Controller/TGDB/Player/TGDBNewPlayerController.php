<?php

namespace App\Controller\TGDB\Player;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBNewPlayerController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    public function action(): ResponseInterface
    {
        return $this->render('tgdb/player/newPlayers.html.twig', [
            'players' => $this->playerRepository->getNewPlayers()
        ]);
    }

}
