<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Death\Repository\DeathRepository;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Round\Service\ConstructTimelineData;
use App\Domain\Stat\Repository\StatRepository;
use App\Enum\RoundState;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundTimelineController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    #[Inject]
    private StatRepository $statRepository;

    #[Inject]
    private DeathRepository $deathRepository;

    public function action(): ResponseInterface
    {
        $round = $this->getArg('id');
        $round = $this->roundRepository->getRound($round);
        if(RoundState::UNDERWAY === $round->getState()) {
            $data = ['round' => $round,];
        } else {
            $data = [
                'round' => $round,
                'stats' => $this->statRepository->getStatsForRound($round->getId(), ['explosion']),
                'deaths' => $this->deathRepository->getDeathsForRound($round->getId()),
                
            ];
        }

        $data = ConstructTimelineData::buildFromData($data, $round);

        return $this->render('round/timeline.html.twig', [
            'data' => $data,
            'round' => $round,
            
        ]);
    }

}
