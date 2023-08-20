<?php

namespace App\Controller\TGDB;

use App\Controller\Controller;
use App\Domain\Note\Repository\NoteRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBController extends Controller
{
    #[Inject]
    private NoteRepository $memos;

    public function action(): ResponseInterface
    {
        $apps = [
            [
                'name' => 'Guide to TLP',
                'icon' => 'fa-solid fa-traffic-light',
                'url' => $this->getUriForRoute('tgdb.tlp'),
            ],
            [
                'name' => 'Notes & Messages',
                'icon' => 'fa-solid fa-envelope',
                'url' => $this->getUriForRoute('tgdb.notes'),
            ],
            [
                'name' => 'Tickets',
                'icon' => 'fa-solid fa-ticket',
                'url' => $this->getUriForRoute('tgdb.tickets'),
            ],
            [
                'name' => 'Your Feedback Link',
                'icon' => 'fa-solid fa-bullhorn',
                'url' => $this->getUriForRoute('tgdb.feedback'),
            ],
        ];
        $memos = $this->memos->getCurrentMemos();
        return $this->render('tgdb/index.html.twig', [
            'narrow' => true,
            'apps' => $apps,
            'memos' => $memos
        ]);
    }

}
