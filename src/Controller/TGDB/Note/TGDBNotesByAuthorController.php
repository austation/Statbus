<?php

namespace App\Controller\TGDB\Note;

use App\Controller\Controller;
use App\Domain\Note\Repository\NoteRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBNotesByAuthorController extends Controller
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): ResponseInterface
    {
        $ckey = $this->getArg('ckey');
        $page = ($this->getArg('page')) ?: 1;
        $notes = $this->noteRepository->getNotesByAuthor($ckey, page: $page)->getResults();
        return $this->render('tgdb/notes/byAuthor.html.twig', [
            'notes' => $notes,
            'narrow' => true,
            'link' => 'tgdb.note',
            'pagination' => [
                'pages' => $this->noteRepository->getPages(),
                'currentPage' => $page,
            ],
            'ckey' => $ckey
        ]);
    }

}
