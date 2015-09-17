<?php

namespace Pagekit\Auth;

use Pagekit\Auth\Event\AuthenticateEvent;
use Pagekit\Auth\Event\AuthorizeEvent;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\Event\LogoutEvent;
use Pagekit\Auth\Exception\BadCredentialsException;
use Pagekit\Event\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Auth
{
    const USERNAME_PARAM = 'username';
    const REDIRECT_PARAM = 'redirect';
    const LAST_USERNAME  = '_auth.last_username';

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var UserProviderInterface
     */
    protected $provider;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var string
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param SessionInterface         $session
     * @param EventDispatcherInterface $events
     */
    public function __construct(EventDispatcherInterface $events, SessionInterface $session = null, $config = null)
    {
        $this->events = $events;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @param  string $var
     * @return string
     */
    public function getName($var = 'user')
    {
        return "_auth.{$var}_".sha1(get_class($this));
    }

    /**
     * Gets the current user
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        if (null !== $this->user) {
            return $this->user;
        }

        if ($user = $this->session->get($this->getName()) and $user instanceof UserInterface) {

            if ($this->session->get($this->getName('lastActive')) + $this->config['timeout'] < time()) {
                $this->logout();
                return false;
            }
            $this->session->set($this->getName('lastActive'), time());

            if ($this->token != $this->session->get($this->getName('token'))) {
                $user = $this->getUserProvider()->find($user->getId());
                $this->session->set($this->getName(), $user);
                $this->session->set($this->getName('token'), $this->token);
            }

            $this->user = $user;
        }

        return $this->user;
    }

    /**
     * Sets the user to be used.
     *
     * @param UserInterface
     */
    public function setUser(UserInterface $user)
    {
        $this->session->set($this->getName(), $user);
        $this->session->set($this->getName('token'), $this->token);
        $this->session->set($this->getName('lastActive'), time());
        $this->user = $user;
    }

    /**
     * Gets the user provider.
     *
     * @throws \RuntimeException
     * @return UserProviderInterface
     */
    public function getUserProvider()
    {
        if (!$this->provider) {
            throw new \RuntimeException('Accessed user provider prior to registering it.');
        }

        return $this->provider;
    }

    /**
     * Sets the user provider.
     *
     * @param UserProviderInterface
     */
    public function setUserProvider(UserProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Gets the session.
     *
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Sets the session.
     *
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Attempts to authenticate the given user according to the passed credentials.
     *
     * @param  array $credentials
     * @return UserInterface
     * @throws BadCredentialsException
     */
    public function authenticate(array $credentials)
    {
        $this->events->trigger(new AuthenticateEvent(AuthEvents::PRE_AUTHENTICATE, $credentials));

        if (!$user = $this->getUserProvider()->findByCredentials($credentials) or !$this->getUserProvider()->validateCredentials($user, $credentials)) {

            $this->session->set(self::LAST_USERNAME, $credentials[self::USERNAME_PARAM]);
            $this->events->trigger(new AuthenticateEvent(AuthEvents::FAILURE, $credentials, $user));

            throw new BadCredentialsException($credentials);
        }

        $this->events->trigger(new AuthenticateEvent(AuthEvents::SUCCESS, $credentials, $user));
        $this->session->remove(self::LAST_USERNAME);

        return $user;
    }

    /**
     * Authorize a user.
     *
     * @param  UserInterface $user
     * @throws Exception\AuthException
     */
    public function authorize(UserInterface $user)
    {
        $this->events->trigger(new AuthorizeEvent(AuthEvents::AUTHORIZE, $user));
    }

    /**
     * Log an user into the application.
     *
     * @param UserInterface $user
     * @return Response
     */
    public function login(UserInterface $user)
    {
        $this->session->migrate();
        $this->setUser($user);

        return $this->events->trigger(new LoginEvent(AuthEvents::LOGIN, $user))->getResponse();
    }

    /**
     * Logs the current user out.
     *
     * @return Response
     */
    public function logout()
    {
        $event = $this->events->trigger(new LogoutEvent(AuthEvents::LOGOUT, $this->user));

        $this->user = null;
        $this->session->invalidate();

        return $event->getResponse();
    }

    /**
     * Sets the token used to identify when to refresh the user from the session
     *
     * @param integer $token
     */
    public function refresh($token = null)
    {
        $this->token = $token;
    }
}
