<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketViewerController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $ticket = $this->ticketRepository->getSingleTicket($this->getArg('round'), $this->getArg('ticket'))->getResults();
        return $this->render('tgdb/tickets/single.html.twig', [
            'ticket' => $ticket,
            
        ]);
    }

}
