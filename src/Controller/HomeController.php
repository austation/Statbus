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

    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }

}
