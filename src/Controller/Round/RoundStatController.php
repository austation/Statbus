<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Round\Service\GetExternalRoundData;
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
        $roundid = $this->getArg('id');
        $stat = $this->getArg('stat');
        $round = $this->roundRepository->getRound($roundid);
        if(in_array($stat, ['sb_who'])) {
            $stat = GetExternalRoundData::getRoundEndData($round);
        } else {
            $stat = $this->statRepository->getRoundStat($round->getId(), $stat);
        }
        return $this->render('round/stat.html.twig', [
            'round' => $round,
            'stat' => $stat,
            'narrow' => true
        ]);
    }

}
