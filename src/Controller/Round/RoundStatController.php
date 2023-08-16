<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundStatController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    #[Inject]
    private StatRepository $statRepository;

    public function action(): ResponseInterface
    {
        $round = $this->getArg('id');
        $stat = $this->getArg('stat');
        return $this->render('round/stat.html.twig', [
            'round' => $this->roundRepository->getRound($round),
            'stat' => $this->statRepository->getRoundStat($round, $stat),
            'narrow' => true
        ]);
    }

}
