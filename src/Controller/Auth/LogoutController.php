<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class LogoutController extends Controller
{
    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $session->invalidate();
        return $this->routeRedirect('home');
    }
}
