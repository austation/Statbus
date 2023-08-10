<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketsForPlayerListingController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $page = ($this->getArg('page')) ?: 1;
        $tickets =$this->ticketRepository->getTicketsByCkey(ckey: $ckey, page: $page)->getResults();
        return $this->render('tgdb/tickets/playerTickets.html.twig', [
            'tickets' => $tickets,
            'link' => 'tgdb.ticket',
            'ckey' => $ckey,
            'narrow' => true,
            'pagination' => [
                'pages' => $this->ticketRepository->getPages(),
                'currentPage' => $page,
            ]
        ]);
    }

}
