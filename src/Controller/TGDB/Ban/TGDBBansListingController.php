<?php

namespace App\Controller\TGDB\Ban;

use App\Controller\Controller;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBBansListingController extends Controller
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;
        $bans = $this->banRepository->getBans($page);
        return $this->render('tgdb/bans/index.html.twig', [
            'narrow' => true,
            'bans' => $bans,
            'link' => 'tgdb.ban.view',
            'pagination' => [
                'pages' => $this->banRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
