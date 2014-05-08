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
    public function onSiteLoaded(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getRequestFormat() != 'html') {
            return;
        }

        $route = $this('url')->route($request->attributes->get('_route'), $request->attributes->get('_route_params', array()));

        if ($route != $request->getRequestUri()) {
            $this('view')->set('head.link.canonical', array('href' => $route));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'site.loaded' => 'onSiteLoaded'
        );
    }
}
