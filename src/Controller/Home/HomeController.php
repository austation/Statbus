<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Admin\Repository\AdminRepository;
use App\Domain\Ban\Repository\BanRepository;
use App\Domain\Death\Repository\DeathRepository;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class HomeController extends Controller
{
    #[Inject]
    private RoundRepository $rounds;

    #[Inject]
    private BanRepository $bans;

    #[Inject]
    private AdminLogRepository $admin;

    #[Inject]
    private StatRepository $stat;

    #[Inject]
    private TicketRepository $ticket;

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
        $apps = [];
        if($this->getUser()) {
            $apps[] = [
                'name' => 'My Player Page',
                'icon' => 'fas fa-user',
                'url' => $this->getUriForRoute('player', ['ckey' => $user->getCkey()]),
                'disabled' => false
            ];
        }
        $apps = [...$apps,
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
                'name' => 'Library',
                'icon' => 'fas fa-book',
                'url' => $this->getUriForRoute('library'),
                'disabled' => ($this->getUser() ? false : true)
            ],
            [
                'name' => 'Citations',
                'icon' => 'fas fa-receipt',
                'url' => "#",
                'disabled' => true
            ],
            [
                'name' => 'Rounds',
                'icon' => 'fas fa-circle',
                'url' => $this->getUriForRoute('rounds'),
                'disabled' => false
            ],
            [
                'name' => 'My Rounds',
                'icon' => 'fas fa-circle-user',
                'url' => $this->getUriForRoute('rounds.player'),
                'disabled' => ($this->getUser() ? false : true)
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

        //Switch for picking a random !FUN! datapoint
        // switch(3) {
        switch(floor(rand(0, 3))) {
            case 0:
                $fun = [
                    'template' => 'newestAdmin.html.twig',
                    'data' => $this->admin->getLatestAdmin()
                ];
                break;

            case 1:
                $fun = [
                    'template' => 'bansByRole.html.twig',
                    'data' => $this->bans->getMostBannedRoles()
                ];
                break;

            case 2:
                $stat = $this->stat->getRandomEntryForKey('played_url')->getResult();
                $data = $stat->getData();
                $dj = array_rand($data);
                $tracks = $data[$dj];
                $track = $tracks[array_rand($tracks)];
                $track['dj'] = $dj;
                if(!$dj) {
                    break;
                }
                $stat->setData($track);
                $fun = [
                    'template' => 'recentMusic.html.twig',
                    'data' => $stat
                ];
                break;

            case 3:
                $stat = $this->ticket->getTicketsByServerLastMonth();
                $fun = [
                    'template' => 'ticketsByServer.html.twig',
                    'data' => $stat
                ];
                break;
                //VERY crashy
                // case 2:
                //     var_dump('death');
                //     $fun = [
                //         'template' => 'lastDeath.html.twig',
                //         'data' => $this->death->getDeaths(1, 1)
                //     ];
                //     break;
        }

        return $this->render('home.html.twig', [
            'narrow' => true,
            'apps' => $apps,
            'rounds' => $this->rounds->getRecentRounds(),
            'server' => pick('basil', 'sybil', 'manuel', 'terry'),
            'fun' => $fun
        ]);
    }

}
