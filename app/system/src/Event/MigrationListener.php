<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Event\EventSubscriberInterface;

class MigrationListener implements EventSubscriberInterface
{
    /**
     * Redirects to migration page on login.
     *
     * @param LoginEvent $event
     */
    public function onLogin(LoginEvent $event)
    {
        if ($event->getUser()->hasAccess('system: software updates') && App::migrator()->create('system:migrations', App::config('system')->get('version'))->get()) {
            $event->setResponse(App::response()->redirect('@system/migration'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'auth.login' => ['onLogin', 8]
        ];
    }
}
