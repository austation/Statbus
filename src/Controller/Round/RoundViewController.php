<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

class RoundViewController extends Controller
{
    public function action(): ResponseInterface
    {
        return $this->render('round/single.html.twig', [
            'round' => $this->getArg('id'),
            'narrow' => true
        ]);
    }

}
