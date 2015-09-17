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
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => 'onUserLogin',
            'terminate' => 'onUserAccess'
        ];
    }
}
