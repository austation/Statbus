<?php

namespace App\Controller\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TicketListingController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;

        $tickets = $this->ticketRepository->getTicketsByCkey($this->getUser()->getCkey(), page: $page)->getResults();
        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'pagination' => [
                'pages' => $this->ticketRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ]
        ]);
    }

}
