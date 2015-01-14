<?php

namespace Pagekit\System\Controller;

use OAuth\Common\Storage\Memory;
use Pagekit\Framework\Application as App;
use Pagekit\Framework\Controller\Controller;

class OAuthController extends Controller
{
    public function connectAction()
    {
        $provider = App::session()->get('oauth.provider');
        $redirect = App::session()->get('oauth.redirect');
        $tokenKey = App::session()->get('oauth.tokenKey');

        $redirect  = explode('?', $redirect);
        $addionalParms = [];
        if (isset($redirect[1])) {
            parse_str($redirect[1],  $addionalParms);
        }
        $redirect = $redirect[0];


        try {

            if (!$service = App::oauth()->createService($provider, [], new Memory)) {
                throw new \Exception("Provider not configured.");
            }

            switch ($service::OAUTH_VERSION) {
                case 1:

                    $oauth_token     = App::request()->query->get('oauth_token');
                    $oauth_verifier  = App::request()->query->get('oauth_verifier');

                    $token = $service->storage->retrieveAccessToken($service->getClass());

                    $token = $service->requestAccessToken(
                        $oauth_token,
                        $oauth_verifier,
                        $token->getRequestTokenSecret()
                    );

                    if(!$token->getAccessToken())
                    {
                        throw new \Exception("Couldn't retrieve token.");
                    }

                    break;

                case 2:

                        $code  = App::request()->query->get('code');
                        $token = $service->requestAccessToken($code);

                    break;
            }

        } catch (\Exception $e) {
            return $this->redirect($redirect, array_merge($addionalParms, ['error' => true, 'message' => $e->getMessage()]));
        }

        if ($tokenKey) {
            App::oauth()->saveToken($provider, $tokenKey, $token);
        }

        return $this->redirect($redirect, array_merge($addionalParms, ['success' => true]));
    }
}
