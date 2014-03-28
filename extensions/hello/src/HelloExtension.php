<?php

namespace Pagekit\Hello;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\System\Event\LinkEvent;
use Pagekit\Widget\Event\RegisterWidgetEvent;


class HelloExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app->on('system.widget', function(RegisterWidgetEvent $event) {
            $event->register('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.dashboard', function(RegisterWidgetEvent $event) use ($app) {
            $event->register('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Hello\HelloLink');
        });
    }
}
