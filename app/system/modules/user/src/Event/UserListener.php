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
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => 'onUserLogin'
        ];
    }
}
