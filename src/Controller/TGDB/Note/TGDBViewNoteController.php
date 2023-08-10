<?php

namespace App\Controller\TGDB\Note;

use App\Controller\Controller;
use App\Domain\Note\Repository\NoteRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBViewNoteController extends Controller
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): ResponseInterface
    {

        $note = $this->noteRepository->getNoteById($this->getArg('id'), true)->getResult();
        return $this->render('tgdb/notes/single.html.twig', [
            'note' => $note,
            'narrow' => true
        ]);
    }

}
