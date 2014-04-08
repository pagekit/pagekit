<?php

namespace Pagekit\Theme\Event;

use Pagekit\Framework\Event\EventSubscriber;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onKernelRequest()
    {
        try {

            $app = self::$app;

            $app['theme.admin'] = $app['themes']->load('system', 'extension://system/theme');
            $app['theme.admin']->boot($app);

            $app['theme.site'] = $app['themes']->load($app['config']->get('theme.site'));
            $app['theme.site']->boot($app);

            $app['view']->setLayout($app[$app['isAdmin'] ? 'theme.admin' : 'theme.site']->getLayout());

        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array('onKernelRequest', 128)
        );
    }
}
