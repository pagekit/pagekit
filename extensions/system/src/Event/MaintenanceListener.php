<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Puts the page in maintenance mode.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemLoaded(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;

        if (App::system()->config('maintenance.enabled') && !(App::isAdmin() || $attributes->get('_maintenance') || App::user()->hasAccess('system: maintenance access'))) {

            $message  = App::system()->config('maintenance.msg') ? : __("We'll be back soon.");
            $response = App::view('extensions/system/modules/theme/templates/maintenance.razr', compact('message'));

            $attributes->set('_disable_profiler_toolbar', true);

            $event->setResponse(App::response($response));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.loaded' => ['onSystemLoaded', 20]
        ];
    }
}
