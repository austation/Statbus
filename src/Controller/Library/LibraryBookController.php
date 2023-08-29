<?php

namespace App\Controller\Library;

use App\Controller\Controller;
use App\Domain\Library\Repository\LibraryActionRepository;
use App\Domain\Library\Repository\LibraryRepository;
use App\Exception\StatbusUnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class LibraryBookController extends Controller
{
    #[Inject]
    private LibraryRepository $library;

    #[Inject]
    private LibraryActionRepository $actionRepository;

    public function action(): ResponseInterface
    {
        $user = $this->getUser();

        $allowDeleted = ($user && $user->has('ADMIN'));

        $book = $this->library->getBook((int) $this->getArg('book'), $allowDeleted);

        if($this->isPOST() && isset($_GET['delete'])) {
            if($user && $user->has('BAN')) {
                $book->deleted = (!$book->isDeleted());
                if($this->library->toggleBookDeletion($book, $user)) {
                    $message = ($book->isDeleted() ? "This book has been deleted" : "This book has been undeleted");
                    $this->addSuccessMessage($message);
                } else {
                    $this->addErrorMessage("Book deletion failed");
                }
                return $this->routeRedirect("library.book", ['book' => $book->id]);
            } else {
                throw new StatbusUnauthorizedException("You do not have permission to perform this action");
            }
        }

        $term = $this->session->get('librarySearch', false);

        $actions = null;
        if($user && $user->has('ADMIN')) {
            $actions = $this->actionRepository->getActionsForBook($book->id);
        }
        return $this->render('library/book.html.twig', [
            'book' => $book,
            'actions' => $actions,
            'term' => $term
        ]);
    }

}
