<?php

namespace Pagekit\OAuth;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Memory;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\ServiceFactory;
use Pagekit\Application as App;
use Pagekit\Routing\Generator\UrlGenerator;

class OAuthHelper
{
    protected $serviceFactory, $services;

    public function __construct()
    {
        $this->serviceFactory = new ServiceFactory();
    }

    /**
     * Open OAuth session and get new token
     *
     * @param  string $provider
     * @param  int $key
     * @param  string $redirect
     * @param  array $scope
     * @return Service|false
     */
    public function create($provider, $key, $redirect, $scope)
    {
        $provider = ucfirst(strtolower($provider));
        $service = $this->createService($provider, $scope, new Memory);

        if (!$service) {
            return false;
        }

        App::session()->set('oauth.redirect', $redirect);
        App::session()->set('oauth.tokenKey', $key);
        App::session()->set('oauth.provider', $provider);

        return $service;
    }

    /**
     * Create OAuth service
     *
     * @param  string $provider
     * @param  array $scope
     * @param  Storage $storage
     * @return Service|false
     */
    public function createService($provider, $scope, $storage)
    {
       $provider = ucfirst(strtolower($provider));
       $service  = $this->serviceFactory->createService($provider, $this->getCredentials($provider), $storage, $scope);

       return $service;
    }

    /**
     * Open OAuth session and load token from storage
     *
     * @param  string $provider
     * @param  int $key
     * @return Provider|false
     */
    public function get($provider, $key)
    {
        $provider = ucfirst(strtolower($provider));

        if (!isset($this->services[$provider.':'.$key])) {
            $storage = new Memory;
            $token   = $this->getToken($provider, $key);
            $service = $this->createService($provider, [], $storage);

            if (!$token || !$service) {
                return false;
            }

            $storage->storeAccessToken($provider, $token);

            if ($token->getEndOfLife() < time()) {
                if ($token->getRefreshToken()) {
                   try {
                       $service->refreshAccessToken($token);
                   } catch (\Exception $e) {
                       return false;
                   }
                } else {
                    return false;
                }
            }

            $this->services[$provider.':'.$key] = $service;
        }

        return $this->services[$provider.':'.$key];
    }

    /**
     * Get provider credentials
     *
     * @param  string $provider
     * @return array
     */
    public function getConfig($provider)
    {
        $providerConfig = App::module('oauth')->config('provider');
        return isset($providerConfig[$provider]) ? $providerConfig[$provider] : [];
    }

    /**
     * Provider configured?
     *
     * @param  string $provider
     * @return bool
     */
    public function isConfigured($provider)
    {
        return (bool) $this->getConfig($provider);
    }

    /**
     * Get token from storage
     *
     * @param  string $provider
     * @param  int $key
     * @return Token
     */
    public function getToken($provider, $key)
    {
        $provider = ucfirst(strtolower($provider));
        $data     = App::config('oauth:token:'.$provider.':'.$key);

        if ($data &&
            array_key_exists('accessToken', $data) &&
            array_key_exists('accessTokenSecret', $data) &&
            array_key_exists('requestToken', $data) &&
            array_key_exists('requestTokenSecret', $data) &&
            array_key_exists('endOfLife', $data) &&
            array_key_exists('extraParams', $data)) {
            $token = new StdOAuth1Token($data['accessToken']);
            $token->setAccessTokenSecret($data['accessTokenSecret']);
            $token->setRequestToken($data['requestToken']);
            $token->setRequestTokenSecret($data['requestTokenSecret']);
            $token->setEndOfLife($data['endOfLife']);
            $token->setExtraParams($data['extraParams']);
        } elseif ($data &&
                  array_key_exists('accessToken', $data) &&
                  array_key_exists('refreshToken', $data) &&
                  array_key_exists('endOfLife', $data) &&
                  array_key_exists('extraParams', $data)) {
            $token = new StdOAuth2Token($data['accessToken'], $data['refreshToken'], null, $data['extraParams']);
            $token->setEndOfLife($data['endOfLife']);
        }

        if (!isset($token) || !$token) {
           return null;
        }

        return $token;
    }

    /**
     * Delete token from storage
     *
     * @param  string $provider
     * @param  int $key
     */
    public function deleteToken($provider, $key)
    {
        $provider = ucfirst(strtolower($provider));
        App::config()->remove('oauth:token:'.$provider.':'.$key);
    }

    /**
     * Save token to storage
     *
     * @param  string $provider
     * @param  int $key
     * @param  Token $token
     */
    public function saveToken($provider, $key, $token)
    {
        $provider = ucfirst(strtolower($provider));

        $data = [];

        if (get_class($token) === 'OAuth\OAuth1\Token\StdOAuth1Token') {
            $data['accessToken']        = $token->getAccessToken();
            $data['accessTokenSecret']  = $token->getAccessTokenSecret();
            $data['requestToken']       = $token->getRequestToken();
            $data['requestTokenSecret'] = $token->getRequestTokenSecret();
            $data['endOfLife']          = $token->getEndOfLife();
            $data['extraParams']        = $token->getExtraParams();
        } else {
            $data['accessToken']  = $token->getAccessToken();
            $data['refreshToken'] = $token->getRefreshToken();
            $data['endOfLife']    = $token->getEndOfLife();
            $data['extraParams']  = $token->getExtraParams();
        }

        App::config()->set('oauth:token:'.$provider.':'.$key, $data);
    }

    /**
     * Get list of provider
     *
     * @return array
     */
    public function getProvider()
    {
        $provider = [];

        foreach (['OAuth1', 'OAuth2'] as $version) {
            foreach (glob(App::path().'/vendor/lusitanian/oauth/src/OAuth/'.$version.'/Service/*.php') as $name) {
               $name = basename($name, '.php');
               if ($name !== 'ServiceInterface' && $name !== 'AbstractService') {
                   $provider[] = $name;
               }
            }
        }

        sort($provider);

        return $provider;
    }

    /**
     * Get provider credentials
     *
     * @param  string $provider
     * @return Credentials
     * @throws \Exception
     */
    public function getCredentials($provider)
    {
        $provider = ucfirst(strtolower($provider));

        if (!$this->isConfigured($provider)) {
            throw new \Exception(ucfirst($provider) . " OAuth not configured.", 1);
        }

        $provider = $this->getConfig($provider);

        $credentials = new Credentials(
            $provider['client_id'],
            $provider['client_secret'],
            $this->getRedirectUrl()
        );

        return $credentials;
    }

    /**
     * Get redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return App::url('@system/oauth/connect', [], true);
    }
}
