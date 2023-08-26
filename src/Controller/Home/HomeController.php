<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Admin\Repository\AdminRepository;
use App\Domain\Ban\Repository\BanRepository;
use App\Domain\Death\Repository\DeathRepository;
use App\Domain\Round\Repository\RoundRepository;
use App\Domain\Stat\Repository\StatRepository;
use App\Service\PolyTalkService;
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
    private DeathRepository $death;

    #[Inject]
    private StatRepository $stat;

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

        //Switch for picking a random !FUN! datapoint

        // switch(2) {
        switch(floor(rand(0, 2))) {
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
                $songs = $data[$dj];
                $song = array_rand($songs);
                if(false !== str_contains($song, 'youtu')) {
                    $song = getYoutubeEmbedUrl($song);
                    $embed = true;
                } else {
                    $embed = false;
                }
                $stat->setData(['dj' => $dj, 'song' => $song, 'embed' => $embed]);
                $fun = [
                    'template' => 'recentMusic.html.twig',
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
            'polytalk' => $polytalk,
            'fun' => $fun
        ]);
    }

}
