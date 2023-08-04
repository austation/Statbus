<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundViewController extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): ResponseInterface
    {
        return $this->render('round/single.html.twig', [
            'round' => $this->roundRepository->getRound($this->getArg('id')),
            'narrow' => true
        ]);
    }

}
