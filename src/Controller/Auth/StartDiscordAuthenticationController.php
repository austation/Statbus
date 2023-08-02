<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Wohali\OAuth2\Client\Provider\Discord;

class StartDiscordAuthenticationController extends Controller
{
    public function action(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['discord'];
        $settings['redirectUri'] = $this->getUriForRoute('auth.discord.check');
        $provider = new Discord($settings);
        $options = [
            'scope' => ['identify']
        ];
        $authUrl = $provider->getAuthorizationUrl($options);
        // var_dump(urldecode($provider->getAuthorizationUrl()));
        // die();
        $session->set('oauth2state', $provider->getState());
        return $this->redirect($authUrl);
    }

    public function xaction(): ResponseInterface
    {
        $session = $this->container->get(Session::class);
        $settings = $this->container->get('settings')['auth']['discord'];
        $settings['redirectUri'] = $route->urlFor('auth.discord.check');
        $provider = new Discord($settings);
        $code = $this->request->getQueryParams();
        if (!isset($this->args['code'])) {
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => ['identify']
            ]);
            $session->set('oauth2state', $provider->getState());
            return $this->redirect($authUrl);

        } elseif (empty($this->args['state']) || ($this->args['state'] !== $session->get('oauth2state'))) {

            $session->set('oauth2state', false);
            exit('Invalid state');

        } else {

            // Step 2. Get an access token using the provided authorization code
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Show some token details
            echo '<h2>Token details:</h2>';
            echo 'Token: ' . $token->getToken() . "<br/>";
            echo 'Refresh token: ' . $token->getRefreshToken() . "<br/>";
            echo 'Expires: ' . $token->getExpires() . " - ";
            echo($token->hasExpired() ? 'expired' : 'not expired') . "<br/>";

            // Step 3. (Optional) Look up the user's profile with the provided token
            try {

                $user = $provider->getResourceOwner($token);

                echo '<h2>Resource owner details:</h2>';
                printf('Hello %s!<br/><br/>', $user->getUsername());
                var_dump($user->toArray());
                var_dump($user);

            } catch (Exception $e) {

                // Failed to get user details
                exit('Oh dear...');

            }
        }
    }

}
