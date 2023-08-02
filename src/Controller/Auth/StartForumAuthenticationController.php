<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Provider\TGForumOAuthProvider;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StartForumAuthenticationController extends Controller
{
    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['forum'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.forum.check');
        $provider = new TGForumOAuthProvider($settings);
        $authUrl = $provider->getAuthorizationUrl();
        $session->set('oauth2state', $provider->getState());
        return $this->redirect($authUrl);
    }

}
