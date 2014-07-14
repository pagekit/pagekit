<?php

namespace Pagekit\User\Event;

use Pagekit\Component\Auth\Event\LoginEvent;
use Pagekit\Framework\Event\EventSubscriber;

class UserListener extends EventSubscriber
{
    const REFRESH_TOKEN = 'user:auth.refresh_token';

    /**
     * Updates the user in the corresponding session.
     */
    public function onUserChange()
    {
        $this['option']->set(self::REFRESH_TOKEN, time(), true);
    }

    /**
     * Updates user's last login time
     */
    public function onUserLogin(LoginEvent $event)
    {
        $this['users']->getUserRepository()->updateLogin($event->getUser());
    }

    /**
     * Updates user's last access time
     */
    public function onUserAccess()
    {
        if ($user = $this['user'] and $user->isAuthenticated()) {
            $this['users']->getUserRepository()->updateAccess($user);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'auth.login'             => 'onUserLogin',
            'kernel.terminate'       => 'onUserAccess',
            'system.role.postSave'   => 'onUserChange',
            'system.role.postDelete' => 'onUserChange',
            'system.user.postSave'   => 'onUserChange',
            'system.user.postDelete' => 'onUserChange'
        ];
    }
}
