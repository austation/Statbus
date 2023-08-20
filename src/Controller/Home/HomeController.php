<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use App\Service\PolyTalkService;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class HomeController extends Controller
{
    #[Inject]
    private RoundRepository $rounds;

    /**
     * action
     *
     * @inheritDoc
     *
     * Renders a list of applications for navigation on the Statbus homepage
     *
     * @return ResponseInterface
     */
    public function action(): ResponseInterface
    {
        $user = $this->getUser();
        $apps = [
            [
                'name' => 'My Bans',
                'icon' => 'fas fa-gavel',
                'url' => $this->getUriForRoute('user.bans'),
                'disabled' => ($this->getUser() ? false : true)
            ],
            [
                'name' => 'My Tickets',
                'icon' => 'fas fa-ticket',
                'url' => $this->getUriForRoute('user.tickets'),
                'disabled' => ($this->getUser() ? false : true)
            ],
            [
                'name' => 'My Notes & Messages',
                'icon' => 'fas fa-envelope',
                'url' => $this->getUriForRoute('user.notes'),
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
        if($user) {
            $apps[] = [
                'name' => 'My Last Round',
                'icon' => 'fas fa-circle',
                'url' => $this->getUriForRoute('round.single', ['id' => $user->getLastRound()]),
                'disabled' => false
            ];
        }
        $polytalk = PolyTalkService::getPolyLine();
        return $this->render('home.html.twig', [
            'narrow' => true,
            'apps' => $apps,
            'rounds' => $this->rounds->getRecentRounds(),
            'polytalk' => $polytalk
        ]);
    }

}
