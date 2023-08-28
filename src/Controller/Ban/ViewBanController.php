<?php

namespace App\Controller\Ban;

use App\Controller\Controller;
use App\Domain\Ban\Repository\BanRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class ViewBanController extends Controller
{
    #[Inject]
    private BanRepository $banRepository;

    public function action(): ResponseInterface
    {
        $user = $this->getUser();
        if(!$user) {
            throw new StatbusUnauthorizedException("You are not logged in.", 403);
        }
        $ban = $this->banRepository->getBanById($this->getArg('id'));
        if($user->getCkey() === $ban->getCkey() || $user->has('BAN')) {
            return $this->render('bans/single.html.twig', [
                
                'ban' => $ban
            ]);
        } else {
            throw new StatbusUnauthorizedException("You do not have permission to view this item.", 403);
        }

    }

}
