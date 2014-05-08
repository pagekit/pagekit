<?php

namespace Pagekit\User\Event;

use Pagekit\Component\Auth\Auth;
use Pagekit\Component\Auth\Event\AuthorizeEvent;
use Pagekit\Component\Auth\Event\LoginEvent;
use Pagekit\Component\Auth\Event\LogoutEvent;
use Pagekit\Component\Auth\Exception\AuthException;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\User\Model\UserInterface;

class AuthorizationListener extends EventSubscriber
{
    /**
     * Logout blocked users
     */
    public function onLoad()
    {
        if ($user = $this('auth')->getUser() and $user->getStatus() == UserInterface::STATUS_BLOCKED) {
            $this('auth')->logout($user);
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
        if ($event->getUser()->getStatus() == UserInterface::STATUS_BLOCKED) {
            throw new AuthException(__('Your account has not been activated or is blocked.'));
        }
    }

    /**
     * Redirects a user after successful login.
     *
     * @param LoginEvent $event
     */
    public function onLogin(LoginEvent $event)
    {
        $event->setResponse($this('response')->redirect($this('request')->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * Redirects a user after successful logout.
     *
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event)
    {
        $event->setResponse($this('response')->redirect($this('request')->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'loaded'         => 'onLoad',
            'auth.authorize' => 'onAuthorize',
            'auth.login'     => array('onLogin', -8),
            'auth.logout'    => array('onLogout', -8)
        );
    }
}
