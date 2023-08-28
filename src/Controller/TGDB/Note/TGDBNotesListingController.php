<?php

namespace App\Controller\TGDB\Note;

use App\Controller\Controller;
use App\Domain\Note\Repository\NoteRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBNotesListingController extends Controller
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;
        $notes = $this->noteRepository->getNotes(page: $page, secret:true)->getResults();
        return $this->render('tgdb/notes/index.html.twig', [
            'notes' => $notes,
            
            'link' => 'tgdb.note',
            'pagination' => [
                'pages' => $this->noteRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
        ]);
    }

}
