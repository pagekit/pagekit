<?php

namespace Pagekit\Theme\Event;

use Pagekit\Framework\Event\EventSubscriber;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onKernelRequest($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        try {

            $app = self::$app;

            $app['theme.admin'] = $app->protect($app['themes']->load('system', 'extension://system/theme'));
            $app['theme.admin']->boot($app);

            $app['theme.site'] = $app->protect($app['themes']->load($app['config']->get('theme.site')));
            $app['theme.site']->boot($app);

        } catch (\Exception $e) {}
    }

    /**
     * Sets the view layout.
     */
    public function onSystemInit()
    {
        $this('view')->setLayout($this($this('isAdmin') ? 'theme.admin' : 'theme.site')->getLayout());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array('onKernelRequest', 60),
            'system.init'    => 'onSystemInit'
        );
    }
}
