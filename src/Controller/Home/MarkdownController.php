<?php

namespace App\Controller\Home;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;

class MarkdownController extends Controller
{
    public function action(): ResponseInterface
    {
        $file = $this->getArg('file');
        $title = $this->getArg('title');
        return $this->render('markdown.html.twig', [
            
            'content' => file_get_contents(__DIR__."/../../../$file"),
            'title' => $title
        ]);
    }

}
