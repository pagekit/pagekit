<?php

namespace Pagekit\View\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

class CanonicalListener implements EventSubscriberInterface
{
    /**
     * Adds a canonical link to the document head.
     *
     * @param GetResponseEvent $event
     */
    public function onSystemSite($event, $request)
    {
        if ($request->getRequestFormat() != 'html') {
            return;
        }

        $route = App::url($request->attributes->get('_route'), $request->attributes->get('_route_params', []));

        if ($route != $request->getRequestUri()) {
            App::view()->meta(['canonical' => $route]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'system.site' => 'onSystemSite'
        ];
    }
}
