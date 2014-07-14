<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\EventSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CanonicalListener extends EventSubscriber
{
    /**
     * Adds a canonical link to the document head.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemSite(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getRequestFormat() != 'html') {
            return;
        }

        $route = $this['url']->route($request->attributes->get('_route'), $request->attributes->get('_route_params', []));

        if ($route != $request->getRequestUri()) {
            $this['view']->set('head.link.canonical', ['href' => $route]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.site' => 'onSystemSite'
        ];
    }
}
