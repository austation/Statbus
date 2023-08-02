<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Domain\User\Service\AuthenticateUser;
use App\Provider\TGForumOAuthProvider;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use DI\Attribute\Inject;

class FinishForumAuthenticationController extends Controller
{
    #[Inject]
    private AuthenticateUser $auth;

    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['forum'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.forum.check');
        $provider = new TGForumOAuthProvider($settings);

        $session = $this->container->get(Session::class);

        if(!$this->getQueryPart('code')) {
            return $this->routeRedirect('auth.forum');
        }

        if (!$this->getQueryPart('state') || ($this->getQueryPart('state') !== $session->get('oauth2state'))) {
            $session->set('oauth2state', false);
            exit('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->getQueryPart('code')
        ]);
        $forumUser = $provider->getResourceOwner($token);
        if($this->auth->authenticateUserFromForum($forumUser->getId())) {
            if($redirect = $session->get('authRedirect')) {
                $session->set('authRedirect', false);
                return $this->redirect($redirect);
            } else {
                return $this->routeRedirect('home');
            }
        } else {
            die("Authentication failed");
        }
    }
}
