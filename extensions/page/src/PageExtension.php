<?php

namespace Pagekit\Page;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Page\Event\RouteListener;
use Pagekit\System\Event\LinkEvent;

class PageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app['events']->addSubscriber(new RouteListener);

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Page\PageLink');
        });
    }
}
