<?php

namespace Pagekit\Hello;

use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Hello\Event\HelloListener;
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

        $this['events']->addSubscriber(new HelloListener());

        $app->on('system.widget', function(RegisterWidgetEvent $event) {
            $event->register('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.dashboard', function(RegisterWidgetEvent $event) {
            $event->register('Pagekit\Hello\HelloWidget');
        });

        $app->on('system.link', function(LinkEvent $event) {
            $event->register('Pagekit\Hello\HelloLink');
        });

        // dispatch event (check Hello\Event\HelloListener to see how subscribers work)
        $app['events']->dispatch('hello.boot');
    }

    public function enable()
    {
        // run all migrations that are newer than the current version
        if ($version = $this['migrator']->create('extension://hello/migrations', $this['option']->get('hello:version'))->run()) {
            $this['option']->set('hello:version', $version);
        }
    }

    public function disable()
    {
        // do nothing
    }

    public function uninstall()
    {
        // drop all own tables (created in migrations)
        $util = $this['db']->getUtility();
        $util->dropTable('@hello_greetings');

        // remove the options setting
        $this['option']->remove('hello:version');
    }
}
