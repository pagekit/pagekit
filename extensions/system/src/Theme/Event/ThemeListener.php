<?php

namespace Pagekit\Theme\Event;

use Pagekit\Application as App;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ThemeListener implements EventSubscriberInterface
{
    /**
     * Loads the site/admin theme.
     */
    public function onSystemInit()
    {
        try {

            $app = App::getInstance();

            $app['theme.site'] = $app['theme']->load($app['config']->get('theme.site'));
            $app['theme.site']->boot($app);

        } catch (\Exception $e) {}
    }

    /**
     * Sets the site layout.
     */
    public function onSystemSite()
    {
        App::view()->setLayout(App::get('theme.site')->getLayout());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init'  => ['onSystemInit', 10],
            'system.site'  => 'onSystemSite'
        ];
    }
}
