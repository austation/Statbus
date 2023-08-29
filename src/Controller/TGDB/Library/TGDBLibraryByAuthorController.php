<?php

namespace App\Controller\TGDB\Library;

use App\Controller\Controller;
use App\Domain\Library\Repository\LibraryRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class TGDBLibraryByAuthorController extends Controller
{
    #[Inject]
    private LibraryRepository $library;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;

        $per_page = 24;

        $ckey = $this->getArg('ckey');

        $books = $this->library->getLibraryByAuthor($ckey, $page, 24);

        return $this->render('tgdb/library/author.html.twig', [
            'books' => $books,
            'ckey' => $ckey,
            'pagination' => [
                'pages' => $this->library->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute('tgdb.library.author', ['ckey' => $ckey])
            ],
            'results' => $this->library->countResults($per_page)
        ]);
    }

}
