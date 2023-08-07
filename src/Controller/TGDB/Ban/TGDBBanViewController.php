<?php

namespace App\Controller\TGDB\Ban;

use App\Controller\Controller;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBBanViewController extends Controller
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): ResponseInterface
    {
        $ban = $this->banRepository->getBanById($this->getArg('id'));
        return $this->render('tgdb/bans/single.html.twig', [
            'narrow' => true,
            'ban' => $ban
        ]);


    }

}
