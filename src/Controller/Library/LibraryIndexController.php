<?php

namespace App\Controller\Library;

use App\Controller\Controller;
use App\Domain\Library\Repository\LibraryRepository;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class LibraryIndexController extends Controller
{
    #[Inject]
    private LibraryRepository $library;

    public function action(): ResponseInterface
    {
        $page = ($this->getArg('page')) ?: 1;

        $per_page = 24;

        $term = $this->session->get('librarySearch', false);

        if(isset($_GET['clear'])) {
            $this->session->set('librarySearch', false);
            $term = false;
            return $this->routeRedirect('library');
        }

        if($this->isPOST()) {
            $search = $this->getRequest()->getParsedBody();
            $term = $search['search'];
            $this->session->set('librarySearch', $term);
        }

        $books = $this->library->getLibrary($page, per_page: $per_page, term: $term);
        if(!$this->getUser() || !$this->getUser()->has('ADMIN')) {
            foreach($books as &$b) {
                $b->redactAuthor();
            }
        }
        return $this->render('library/index.html.twig', [
            'books' => $books,
            'term' => $term,
            'pagination' => [
                'pages' => $this->library->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ],
            'results' => $this->library->countResults($per_page)
        ]);
    }

}
