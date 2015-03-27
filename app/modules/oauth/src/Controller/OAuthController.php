<?php

namespace Pagekit\OAuth\Controller;

use OAuth\Common\Storage\Memory;
use Pagekit\Application as App;
use Pagekit\Application\Controller;

class OAuthController extends Controller
{
    public function connectAction()
    {
        $provider = App::session()->get('oauth.provider');
        $redirect = App::session()->get('oauth.redirect');
        $tokenKey = App::session()->get('oauth.tokenKey');

        $redirect = explode('?', $redirect);
        $additionalParms = [];

        if (isset($redirect[1])) {
            parse_str($redirect[1], $additionalParms);
        }
        $redirect = $redirect[0];

        try {

            if (!$service = App::oauth()->createService($provider, [], new Memory)) {
                throw new \Exception("Provider not configured.");
            }

            switch ($service::OAUTH_VERSION) {
                case 1:

                    $oauth_token = App::request()->query->get('oauth_token');
                    $oauth_verifier = App::request()->query->get('oauth_verifier');

                    $token = $service->storage->retrieveAccessToken($service->getClass());

                    $token = $service->requestAccessToken(
                        $oauth_token,
                        $oauth_verifier,
                        $token->getRequestTokenSecret()
                    );

                    if (!$token->getAccessToken()) {
                        throw new \Exception("Couldn't retrieve token.");
                    }

                    break;

                case 2:
                    $code = App::request()->query->get('code');
                    $token = $service->requestAccessToken($code);

                    break;
            }

        } catch (\Exception $e) {
            return $this->redirect($redirect, array_merge($additionalParms, ['error' => true, 'message' => $e->getMessage()]));
        }

        if ($tokenKey) {
            App::oauth()->saveToken($provider, $tokenKey, $token);
        }

        return $this->redirect($redirect, array_merge($additionalParms, ['success' => true]));
    }
}
