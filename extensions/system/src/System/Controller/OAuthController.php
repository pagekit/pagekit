<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use OAuth\Common\Storage\Memory;

/**
 * @Route("/oauth")
 */
class OAuthController extends Controller
{

    public function connectAction()
    {
        $provider = $this['session']->get('oauth.provider');
        $redirect = $this['session']->get('oauth.redirect');
        $tokenKey = $this['session']->get('oauth.tokenKey');

        $redirect  = explode('?', $redirect);
        $addionalParms = [];
        if (isset($redirect[1])) {
            parse_str($redirect[1],  $addionalParms);
        }
        $redirect = $redirect[0];


        try {

            if (!$service = $this['oauth']->createService($provider, [], new Memory)) {
                throw new \Exception("Provider not configured.");
            }

            switch ($service::OAUTH_VERSION) {
                case 1:

                    $oauth_token     = $this['request']->query->get('oauth_token');
                    $oauth_verifier  = $this['request']->query->get('oauth_verifier');

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

                        $code  = $this['request']->query->get('code');
                        $token = $service->requestAccessToken($code);

                    break;
            }

        } catch (\Exception $e) {
            return $this->redirect($redirect, array_merge($addionalParms, ['error' => true, 'message' => $e->getMessage()]));
        }

        if ($tokenKey) {
            $this['oauth']->saveToken($provider, $tokenKey, $token);
        }

        return $this->redirect($redirect, array_merge($addionalParms, ['success' => true]));
    }
}
