<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Enum\RoundState;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundsForCkeyController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;
        $ckey = $this->getArg('ckey');
        $rounds = $this->roundRepository->getRoundsForCkey($ckey, $page);
        return $this->render('round/ckey.html.twig', [
            'rounds' => $rounds,
            'pagination' => [
                'pages' => $this->roundRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName(), ['ckey' => $ckey]),
            ],
            'ckey' => $ckey
        ]);
    }

}
