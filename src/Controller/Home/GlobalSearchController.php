<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use App\Domain\Round\Repository\RoundRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class GlobalSearchController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;

    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): ResponseInterface
    {
        $data = [];
        $term = $this->getRequest()->getParsedBody()['term'];
        $data['ckeys'] = $this->playerRepository->ckeySearch($term);
        $data['rounds'] = $this->roundRepository->roundSearch($term);

        return $this->json([
            'term' => $term,
            'results' => [...$data['ckeys'],...$data['rounds']]
        ]);
    }

}
