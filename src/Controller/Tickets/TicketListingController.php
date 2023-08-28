<?php

namespace App\Controller\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TicketListingController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;
        if($user = $this->getUser()) {
            $tickets = $this->ticketRepository->getTicketsByCkey($this->getUser()->getCkey(), page: $page)->getResults();
            return $this->render('tickets/index.html.twig', [
                'tickets' => $tickets,
                
                'pagination' => [
                    'pages' => $this->ticketRepository->getPages(),
                    'currentPage' => $page,
                    'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
                ]
            ]);
        } else {
            throw new StatbusUnauthorizedException("You must be logged in to view this page.", 403);
        }
    }

}
