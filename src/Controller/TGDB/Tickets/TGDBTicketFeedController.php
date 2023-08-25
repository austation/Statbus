<?php

namespace App\Controller\TGDB\Tickets;

use App\Controller\Controller;
use App\Domain\Ticket\Repository\TicketRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBTicketFeedController extends Controller
{
    #[Inject]
    private TicketRepository $ticketRepository;

    public function action(): ResponseInterface
    {
        if($this->isPOST()) {
            $date = $this->getRequest()->getParsedBody()['since'];
            return $this->json([
                'data' => $this->ticketRepository->getTicketsSinceDate($date)->getResults()
            ]);
        }
        if(isset($_GET['json'])) {
            return $this->json([
                'data' => $this->ticketRepository->getTicketFeed(1, 10)->getResults()
            ]);
        }
        return $this->render('tgdb/tickets/live.html.twig');
    }

}
