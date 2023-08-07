<?php

namespace App\Controller\TGDB;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

class TGDBController extends Controller
{
    public function action(): ResponseInterface
    {
        $apps = [
            [
                'name' => 'Guide to TLP',
                'icon' => 'fa-solid fa-traffic-light',
                'url' => $this->getUriForRoute('tgdb.tlp'),
            ],
            [
                'name' => 'Tickets',
                'icon' => 'fa-solid fa-ticket',
                'url' => $this->getUriForRoute('tgdb.tickets'),
            ],
            [
                'name' => 'Your Feedback Link',
                'icon' => 'fa-solid fa-bullhorn',
                'url' => '#',
                'disabled' => true
            ],
        ];
        return $this->render('tgdb/index.html.twig', [
            'narrow' => true,
            'apps' => $apps
        ]);
    }

}
