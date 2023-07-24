<?php

namespace App\Controller;

use App\Controller;
use Slim\Http\Response;

class HomeController extends Controller
{
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

}
