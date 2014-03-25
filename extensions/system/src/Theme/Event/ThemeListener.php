<?php

namespace Pagekit\Theme\Event;

use Pagekit\Framework\Event\EventSubscriber;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onBoot()
    {
        try {

            $app = self::$app;
            $app['theme.admin'] = $app['themes']->load('system', 'extension://system/theme');
            $app['theme.site']  = $app['themes']->load($app['config']->get('theme.site'));

            $theme = $app['isAdmin'] ? $app['theme.admin'] : $app['theme.site'];
            $theme->boot($app);

            $app['view']->setLayout($theme->getLayout());

        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'boot' => array('onBoot', 16)
        );
    }
}
