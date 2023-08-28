<?php

namespace App\Controller\Library;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminRepository;
use App\Domain\Library\Repository\LibraryRepository;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class LibraryIndexController extends Controller
{
    #[Inject]
    private LibraryRepository $library;

    public function action(): ResponseInterface
    {
        $books = $this->library->getLibrary();
        return $this->render('library/index.html.twig', [
            'books' => $books,
        ]);
    }

}
