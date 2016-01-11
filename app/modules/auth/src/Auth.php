<?php

namespace Pagekit\Auth;

use Pagekit\Auth\Event\AuthenticateEvent;
use Pagekit\Auth\Event\AuthorizeEvent;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\Event\LogoutEvent;
use Pagekit\Auth\Exception\BadCredentialsException;
use Pagekit\Auth\Handler\HandlerInterface;
use Pagekit\Event\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    const LAST_USERNAME     = '_auth.last_username';

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var UserProviderInterface
     */
    protected $provider;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param HandlerInterface         $handler
     */
    public function __construct(EventDispatcherInterface $events, HandlerInterface $handler)
    {
        $this->events = $events;
        $this->handler = $handler;
    }

    /**
     * Gets the current user.
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        if ($this->user === null && $id = $this->handler->read()) {
            $this->user = $this->getUserProvider()->find($id);
        }

        return $this->user;
    }

    /**
     * Sets the user to be used.
     *
     * @param  UserInterface $user
     * @param  bool          $remember
     */
    public function setUser(UserInterface $user, $remember = false)
    {
        $this->handler->write($user->getId(), $remember);
        $this->user = $user;
    }

    /**
     * Removes the user.
     *
     * @param UserInterface
     */
    public function removeUser()
    {
        $this->handler->destroy();
        $this->user = null;
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
     * Attempts to authenticate the given user according to the passed credentials.
     *
     * @param  array $credentials
     * @return UserInterface
     * @throws BadCredentialsException
     */
    public function authenticate(array $credentials)
    {
        $this->events->trigger(new AuthenticateEvent(AuthEvents::PRE_AUTHENTICATE, $credentials));

        if (!$user = $this->getUserProvider()->findByCredentials($credentials)
            or !$this->getUserProvider()->validateCredentials($user, $credentials)
        ) {
            $this->events->trigger(new AuthenticateEvent(AuthEvents::FAILURE, $credentials, $user));

            throw new BadCredentialsException($credentials);
        }

        $this->events->trigger(new AuthenticateEvent(AuthEvents::SUCCESS, $credentials, $user));

        return $user;
    }

    /**
     * Authorizes a user.
     *
     * @param  UserInterface $user
     * @throws Exception\AuthException
     */
    public function authorize(UserInterface $user)
    {
        $this->events->trigger(new AuthorizeEvent(AuthEvents::AUTHORIZE, $user));
    }

    /**
     * Logs an user into the application.
     *
     * @param  UserInterface $user
     * @param  bool          $remember
     * @return Response
     */
    public function login(UserInterface $user, $remember = false)
    {
        $this->setUser($user, $remember);

        return $this->events->trigger(new LoginEvent(AuthEvents::LOGIN, $user));
    }

    /**
     * Logs the current user out.
     *
     * @return Response
     */
    public function logout()
    {
        $event = $this->events->trigger(new LogoutEvent(AuthEvents::LOGOUT, $this->getUser()));
        $this->removeUser();

        return $event;
    }
}
