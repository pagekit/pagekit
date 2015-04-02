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
        $app = App::getInstance();
        $theme = $app['module']['system']->config('theme.site');

        $app['module']->load($theme);
        if ($app['theme.site'] = $app['module']->get($theme)) {
            $app->on('system.site', function () use ($app) {
                $app['view']->setLayout($app['theme.site']->getLayout());
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.init' => ['onSystemInit', 10]
        ];
    }
}
