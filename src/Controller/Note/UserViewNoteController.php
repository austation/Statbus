<?php

namespace App\Controller\Note;

use App\Controller\Controller;
use App\Domain\Note\Repository\NoteRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class UserViewNoteController extends Controller
{
    #[Inject]
    private NoteRepository $noteRepository;

    public function action(): ResponseInterface
    {
        $user = $this->getUser();
        $note = $this->noteRepository->getNoteById($this->getArg('id'))->getResult();
        if($note->getCkey() !== $user->getCkey()) {
            throw new StatbusUnauthorizedException("This note does not belong to you", 403);
        }
        return $this->render('notes/single.html.twig', [
            'note' => $note,
            'narrow' => true
        ]);
    }

}
