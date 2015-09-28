<?php

namespace Pagekit\User\Event;

use Pagekit\Application as App;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\User\Model\User;

class UserListener implements EventSubscriberInterface
{
    /**
     * Updates user's last login time
     */
    public function onUserLogin(LoginEvent $event)
    {
        User::updateLogin($event->getUser());
    }

    /**
     * Updates user's last access time
     */
    public function onUserAccess()
    {
        if ($user = App::user() and $user->isAuthenticated()) {
            User::updateAccess($user);
        }
    }

    /**
     * Updates the user in the corresponding session.
     */
    public function onUserChange()
    {
        App::config('system/user')->set('auth.refresh_token', App::get('auth.random')->generateString(16));
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => 'onUserLogin',
            'terminate' => 'onUserAccess',
            'terminate' => 'onUserAccess',
            'model.role.saved' => 'onUserChange',
            'model.role.deleted' => 'onUserChange',
            'model.user.saved' => 'onUserChange',
            'model.user.deleted' => 'onUserChange'
        ];
    }
}
