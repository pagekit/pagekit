<?php

namespace Pagekit\User\Event;

use Pagekit\Application as App;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\User\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserListener implements EventSubscriberInterface
{
    const REFRESH_TOKEN = 'user:auth.refresh_token';

    /**
     * Updates the user in the corresponding session.
     */
    public function onUserChange()
    {
        App::option()->set(self::REFRESH_TOKEN, time(), true);
    }

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
    public static function getSubscribedEvents()
    {
        return [
            'auth.login'           => 'onUserLogin',
            'kernel.terminate'     => 'onUserAccess',
            'user.role.postSave'   => 'onUserChange',
            'user.role.postDelete' => 'onUserChange',
            'user.postSave'        => 'onUserChange',
            'user.postDelete'      => 'onUserChange'
        ];
    }
}
