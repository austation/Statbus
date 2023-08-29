<?php

namespace App\Controller\Ban;

use App\Controller\Controller;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class UserBanController extends Controller
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): ResponseInterface
    {
        $user = $this->getUser();
        if(!$user) {
            throw new StatbusUnauthorizedException("You are not logged in.", 403);
        }
        $bans = $this->banRepository->getBansForCkey($user->getCkey());
        return $this->render('bans/index.html.twig', [

            'bans' => $bans
        ]);
    }

}
