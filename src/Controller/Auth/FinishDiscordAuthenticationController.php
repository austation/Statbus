<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Domain\Discord\Repository\DiscordRepository;
use App\Domain\User\Service\AuthenticateUser;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Wohali\OAuth2\Client\Provider\Discord;
use DI\Attribute\Inject;

class FinishDiscordAuthenticationController extends Controller
{
    #[Inject]
    private AuthenticateUser $auth;

    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['discord'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.discord.check');
        $provider = new Discord($settings);

        $session = $this->container->get(Session::class);

        if(!$this->getQueryPart('code')) {
            return $this->routeRedirect('auth.discord');
        }

        if (!$this->getQueryPart('state') || ($this->getQueryPart('state') !== $session->get('oauth2state'))) {
            $session->set('oauth2state', false);
            exit('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $this->getQueryPart('code')
        ]);
        $discordUser = $provider->getResourceOwner($token)->toArray();
        if($this->auth->authenticateUserFromDiscord($discordUser['id'])) {
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
