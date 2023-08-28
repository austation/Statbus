<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketRoundListingController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $round = $this->getArg('round');
        $page = ($this->getArg('page')) ?: 1;
        $tickets =$this->ticketRepository->getTicketsForRound($round, page: $page)->getResults();
        return $this->render('tgdb/tickets/index.html.twig', [
            'tickets' => $tickets,
            'link' => 'tgdb.ticket',
            'round' => $round,
            
            'pagination' => [
                'pages' => $this->ticketRepository->getPages(),
                'currentPage' => $page,
                //TODO: Figure out a solution for this.
                // 'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ]
        ]);
    }

}
