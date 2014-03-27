<?php

namespace Pagekit\Hello;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\LinkEvent;
use Pagekit\System\Event\DashboardEvent;

class HelloExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app->on('init', function() use ($app) {
            $app['widgets']->registerType('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.dashboard', function(DashboardEvent $event) use ($app) {
            $event->registerType('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Hello\HelloLink');
        });
    }
}
