<?php

namespace App\Controller\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use App\Domain\Ticket\Service\GetTicketCkeys;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TicketViewerController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {

        $ticket = $this->ticketRepository->getSingleTicket($this->getArg('round'), $this->getArg('ticket'))->getResults();
        if(false == GetTicketCkeys::isCkeyInTicket($ticket, $this->getUser()->getCkey())) {
            throw new StatbusUnauthorizedException("You do not have permission to view this ticket", 403);
        }
        return $this->render('tickets/single.html.twig', [
            'ticket' => $ticket,
            'narrow' => true
        ]);
    }

}
