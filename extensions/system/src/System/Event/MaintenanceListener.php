<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener extends EventSubscriber
{
    /**
     * Puts the page in maintenance mode.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemLoaded(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;

        if ($this['config']->get('maintenance.enabled') && !($this['isAdmin'] || $attributes->get('_maintenance') || $this['user']->hasAccess('system: maintenance access'))) {

            $message  = $this['config']->get('maintenance.msg') ? : __("We'll be back soon.");
            $response = $this['view']->render('extension://system/theme/templates/maintenance.razr', compact('message'));

            $attributes->set('_disable_profiler_toolbar', true);

            $event->setResponse($this['response']->create($response));
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
