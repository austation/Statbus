<?php

namespace App\Controller\Round;

use App\Controller\Controller;
use App\Domain\Round\Repository\RoundRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class RoundLogsRedirect extends Controller
{
    #[Inject]
    private RoundRepository $roundRepository;

    public function action(): ResponseInterface
    {
        $round = $this->getArg('id');
        $round = $this->roundRepository->getRound($round);
        $link = $round->getPublicLogs();
        // if($this->getUser()->has('ADMIN')) {
        //     $link = $round->getAdminLogs();
        // }
        return $this->redirect($link);
    }

}
