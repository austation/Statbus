<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundViewController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    #[Inject]
    private StatRepository $statRepository;

    public function action(): ResponseInterface
    {
        $round = $this->getArg('id');
        return $this->render('round/single.html.twig', [
            'round' => $this->roundRepository->getRound($round),
            'stats' => $this->statRepository->getStatsForRound($round, ['antagonists','testmerged_prs','commendation']),
            'statlist' => $this->statRepository->listStatsForRound($round),
            'narrow' => true
        ]);
    }

}
