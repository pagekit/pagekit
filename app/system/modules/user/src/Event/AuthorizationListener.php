<?php

namespace Pagekit\User\Event;

use Pagekit\Application as App;
use Pagekit\Auth\Auth;
use Pagekit\Auth\Event\AuthorizeEvent;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\Event\LogoutEvent;
use Pagekit\Auth\Exception\AuthException;
use Pagekit\User\Auth\UserProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthorizationListener implements EventSubscriberInterface
{
    /**
     * Initialize system.
     */
    public function onSystemInit()
    {
        App::auth()->setUserProvider(new UserProvider(App::get('auth.password')));
        App::auth()->refresh(App::config(UserListener::REFRESH_TOKEN));
    }

    /**
     * Logout blocked users.
     */
    public function onSystemLoaded()
    {
        if ($user = App::auth()->getUser() and $user->isBlocked()) {
            App::auth()->logout($user);
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
        $event->setResponse(App::response()->redirect(App::request()->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * Redirects a user after successful logout.
     *
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event)
    {
        $event->setResponse(App::response()->redirect(App::request()->get(Auth::REDIRECT_PARAM)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'    => ['onSystemInit', 20],
            'system.loaded'  => 'onSystemLoaded',
            'auth.authorize' => 'onAuthorize',
            'auth.login'     => ['onLogin', -8],
            'auth.logout'    => ['onLogout', -8]
        ];
    }
}
