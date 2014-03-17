<?php

namespace Pagekit\Hello;

use Pagekit\Framework\Application;
use Pagekit\Package\Extension;

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
            $app['links']->register('Pagekit\Hello\HelloLink');
        });
    }
}
