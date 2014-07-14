<?php

namespace Pagekit\User\Event;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\Event\AuthorizeEvent;
use Pagekit\Component\Auth\Event\LoginEvent;
use Pagekit\Component\Auth\Event\LogoutEvent;
use Pagekit\Component\Auth\Exception\AuthException;
use Pagekit\Framework\Event\EventSubscriber;

class AuthorizationListener extends EventSubscriber
{
    /**
     * Logout blocked users.
     */
    public function onSystemLoaded()
    {
        if ($user = $this['auth']->getUser() and $user->isBlocked()) {
            $this['auth']->logout($user);
        }
    }

    /**
     * Blocks users that are either not activated or blocked.
     *
     * @param  AuthorizeEvent $event
     * @throws AuthException
     */
    public function onAuthorize(AuthorizeEvent $event)
    {
        if ($event->getUser()->isBlocked()) {
            throw new AuthException($event->getUser()->getAccess() ? __('Your account is blocked.') : __('Your account has not been activated.'));
        }
    }

    /**
     * Redirects a user after successful login.
     *
     * @param LoginEvent $event
     */
    public function onLogin(LoginEvent $event)
    {
        $event->setResponse($this['response']->redirect($this['request']->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * Redirects a user after successful logout.
     *
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event)
    {
        $event->setResponse($this['response']->redirect($this['request']->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.loaded'  => 'onSystemLoaded',
            'auth.authorize' => 'onAuthorize',
            'auth.login'     => ['onLogin', -8],
            'auth.logout'    => ['onLogout', -8]
        ];
    }
}
