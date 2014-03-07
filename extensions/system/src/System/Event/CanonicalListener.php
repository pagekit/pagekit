<?php

namespace Pagekit\System\Event;

use Pagekit\Component\Routing\UrlGenerator;
use Pagekit\Framework\Event\EventSubscriber;

class CanonicalListener extends EventSubscriber
{
    /**
     * Adds a canonical link to the document head.
     */
    public function onSiteInit()
    {
        $request = $this('request');

        if ($request->getRequestFormat() != 'html') {
            return;
        }

        $route = $this('url')->to($request->attributes->get('_route'), $request->attributes->get('_route_params', array()));

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
            'site.init' => 'onSiteInit'
        );
    }
}
