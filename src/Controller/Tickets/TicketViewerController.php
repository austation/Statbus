<?php

namespace App\Controller\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use App\Domain\Ticket\Service\GetTicketCkeys;
use App\Exception\StatbusNotYourTicketException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TicketViewerController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {

        $ticket = $this->ticketRepository->getSingleTicket($this->getArg('round'), $this->getArg('ticket'))->getResults();
        if(!$this->getUser() || false == GetTicketCkeys::isCkeyInTicket($ticket, $this->getUser()->getCkey())) {
            return $this->render('error.html.twig', [
                'error' => new StatbusNotYourTicketException("This ticket does not belong to you", 403),
                'class' => 'App/Exception/StatbusNotYourTicketException',
                'code' => 403,
                'narrow' => true,
                'link' => $this->getUriForRoute('tgdb.ticket', [
                    'round' => $this->getArg('round'),
                    'ticket' => $this->getArg('ticket')
                ])
            ]);
        }
        return $this->render('tickets/single.html.twig', [
            'ticket' => $ticket,
            'narrow' => true
        ]);
    }

}
