<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Puts the page in maintenance mode.
     */
    public function onKernelRequest($event, $request)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (App::system()->config('maintenance.enabled') && !(App::isAdmin() || $request->attributes->get('_maintenance') || App::user()->hasAccess('system: maintenance access'))) {

            $message  = App::system()->config('maintenance.msg') ? : __("We'll be back soon.");
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
            'kernel.request' => ['onKernelRequest', 10]
        ];
    }
}
