<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

class HomeController extends Controller
{
    // /
    public function action(): ResponseInterface
    {
        $apps = [
            [
                'name' => 'Bans',
                'icon' => 'fas fa-gavel',
                'url' => $this->getUriForRoute('user.bans'),
                'disabled' => ($this->getUser() ? false : true)
            ],
            [
                'name' => 'Tickets',
                'icon' => 'fas fa-gavel',
                'url' => $this->getUriForRoute('user.tickets'),
                'disabled' => ($this->getUser() ? false : true)
            ],
            [
                'name' => 'Citations',
                'icon' => 'fas fa-receipt',
                'url' => "#",
                'disabled' => true
            ],
            [
                'name' => 'BadgeR',
                'icon' => 'fas fa-id-card',
                'url' => "https://badger.statbus.space",
                'disabled' => false
            ],
            [
                'name' => 'Renderbus',
                'icon' => 'fas fa-location-dot',
                'url' => "https://renderbus.statbus.space",
                'disabled' => false
            ],
        ];
        return $this->render('home.html.twig', [
            'narrow' => true,
            'apps' => $apps
        ]);
    }

}
