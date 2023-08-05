<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketListingController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;
        $tickets =$this->ticketRepository->getTickets(page: $page)->getResults();
        return $this->render('tgdb/tickets/index.html.twig', [
            'tickets' => $tickets,
            'link' => 'tgdb.ticket',
            'pagination' => [
                'pages' => $this->ticketRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ]
        ]);
    }

}
