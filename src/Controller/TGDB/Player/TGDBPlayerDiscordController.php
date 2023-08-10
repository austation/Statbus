<?php

namespace App\Controller\TGDB\Player;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBPlayerDiscordController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $discord = $this->playerRepository->getDiscordVerificationsForCkey($ckey);
        return $this->json([
            'discord' => $discord,
        ]);
    }

}
