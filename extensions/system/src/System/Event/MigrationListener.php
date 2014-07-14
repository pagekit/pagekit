<?php

namespace Pagekit\System\Event;

use Pagekit\Component\Auth\Event\LoginEvent;
use Pagekit\Framework\Event\EventSubscriber;

class MigrationListener extends EventSubscriber
{
    /**
     * Redirects to migration page on login.
     *
     * @param LoginEvent $event
     */
    public function onLogin(LoginEvent $event)
    {
        if ($event->getUser()->hasAccess('system: software updates') && $this['migrator']->create('extension://system/migrations', $this['option']->get('system:version'))->get()) {
            $event->setResponse($this['response']->redirect('@system/migration'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'auth.login' => ['onLogin', 8]
        ];
    }
}
