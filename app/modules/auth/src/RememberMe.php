<?php

namespace Pagekit\Auth;


use Pagekit\Auth\Exception\AuthException;
use Symfony\Component\HttpFoundation\Request;

class RememberMe
{
    const REMEMBER_ME_PARAM = '_remember_me';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param $session
     * @param $config
     */
    public function __construct($session, $config)
    {
        $this->session = $session;
        $this->config = $config;
    }


    /**
     * Get a unique identifier for the auth session value.
     *
     * @param  string $var
     * @return string
     */
    public function getKey($var = 'user')
    {
        return "_rememberme.{$var}_" . sha1(get_class($this));
    }

    /**
     * Tries to read the username from the session.
     *
     * @param  UserProviderInterface $provider
     * @return UserInterface
     * @throws AuthException
     */
    public function autoLogin(UserProviderInterface $provider)
    {
        try {

            if (null === $username = $this->session->get($this->getKey('username'))) {
                throw new AuthException('No remember me cookie found.');
            }

            if ($this->session->getLastActive() + $this->config['rememberme_lifetime'] < time()) {
                throw new AuthException('The cookie has expired.');
            }

            if (!$user = $provider->findByUsername($username)) {
                throw new AuthException(sprintf('No user found for "%s".', $username));
            }

        } catch (AuthException $e) {

            $this->remove();

            throw $e;
        }

        return $user;
    }

    /**
     * This is called when an authentication is successful.
     *
     * @param Request $request
     * @param UserInterface $user
     */
    public function set(Request $request, UserInterface $user)
    {
        if (!$this->isRememberMeRequested($request)) {
            return;
        }

        $this->session->set($this->getKey('username'), $user->getUsername());
        $this->session->setLifetime($this->config['rememberme_lifetime']);
    }


    /**
     * Deletes the remember-me
     */
    public function remove()
    {
        $this->session->remove($this->getKey('username'));
        $this->session->setLifetime($this->config['gc_maxlifetime']);
    }

    /**
     * Checks whether remember-me capabilities where requested
     *
     * @param Request $request
     * @return Boolean
     */
    protected function isRememberMeRequested(Request $request)
    {
        $parameter = $request->get(self::REMEMBER_ME_PARAM, null, true);

        return $parameter === 'true' || $parameter === 'on' || $parameter === '1' || $parameter === 'yes';
    }
}
