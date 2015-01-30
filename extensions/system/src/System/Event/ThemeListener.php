<?php

namespace Pagekit\System\Event;

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
            $theme = $app['config']->get('theme.site');

            $app['module']->load($theme);
            $app['theme.site'] = $app['module']->get($theme);

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
