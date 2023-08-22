<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use App\Domain\Player\Repository\PlayerRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class GlobalSearchController extends Controller
{
    #[Inject]
    private PlayerRepository $playerRepository;
    public function action(): ResponseInterface
    {
        $term = $this->getRequest()->getParsedBody()['term'];
        $ckeys = $this->playerRepository->ckeySearch($term);
        return $this->json([
            'term' => $term,
            'ckeys' => $ckeys
        ]);
    }

}
