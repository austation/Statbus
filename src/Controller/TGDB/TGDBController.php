<?php

namespace App\Controller\TGDB;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

class TGDBController extends Controller
{
    public function action(): ResponseInterface
    {
        return $this->render('tgdb/index.html.twig', [
            'narrow' => true
        ]);
    }

}
