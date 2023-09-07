<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Enum\RoundState;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundsForPlayerController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): ResponseInterface
    {
        $user = $this->getUser();
        $page = ($this->getArg('page')) ?: 1;
        $rounds = $this->roundRepository->getRoundsForCkey($user->getCkey(), $page);
        return $this->render('round/myrounds.html.twig', [
            'rounds' => $rounds,
            'pagination' => [
                'pages' => $this->roundRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ]
        ]);
    }

}
