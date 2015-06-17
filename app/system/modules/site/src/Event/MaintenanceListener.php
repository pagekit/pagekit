<?php

namespace Pagekit\Site\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Puts the page in maintenance mode.
     */
    public function onRequest($event, $request)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $site = App::module('system/site');

        if ($site->config('maintenance.enabled') && !(App::isAdmin() || $request->attributes->get('_maintenance') || App::user()->hasAccess('site: maintenance access'))) {

            $message  = $site->config('maintenance.msg') ? : __("We'll be back soon.");
            $response = App::view('system/theme:templates/maintenance.php', compact('message'));

            $request->attributes->set('_disable_profiler_toolbar', true);

            $event->setResponse(App::response($response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'app.request' => ['onRequest', 10]
        ];
    }
}
