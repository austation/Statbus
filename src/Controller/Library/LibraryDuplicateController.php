<?php

namespace App\Controller\Library;

use App\Controller\Controller;
use App\Domain\Library\Repository\LibraryRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class LibraryDuplicateController extends Controller
{
    #[Inject]
    private LibraryRepository $library;

    public function action(): ResponseInterface
    {
        $books = $this->library->getDuplicateBooks();
        foreach($books as &$b){
            $b->ids = explode(',',$b->ids);
        }
        return $this->render('library/dupes.html.twig', [
            'books' => $books,
        ]);
    }

}
