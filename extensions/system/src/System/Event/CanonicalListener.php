<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CanonicalListener implements EventSubscriberInterface
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

        $route = App::url()->route($request->attributes->get('_route'), $request->attributes->get('_route_params', []));

        if ($route != $request->getRequestUri()) {
            App::view()->set('head.link.canonical', ['href' => $route]);
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
