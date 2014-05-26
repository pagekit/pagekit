<?php

namespace Pagekit\Theme\Event;

use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Theme\Package\ThemeLoader;
use Pagekit\Theme\Package\ThemeRepository;
use Pagekit\Theme\ThemeManager;

class ThemeListener extends EventSubscriber
{
    /**
     * Loads the site/admin theme.
     */
    public function onSystemInit($event)
    {
        try {

            $app = self::$app;

            $app['themes'] = function($app) {

                $loader     = new ThemeLoader;
                $repository = new ThemeRepository($app['config']['theme.path'], $loader);
                $installer  = new PackageInstaller($repository, $loader);

                return new ThemeManager($app, $repository, $installer, $app['autoloader'], $app['locator']);
            };

            $app['theme.admin'] = $app->protect($app['themes']->load('system', 'extension://system/theme'));
            $app['theme.admin']->boot($app);

            $app['theme.site'] = $app->protect($app['themes']->load($app['config']->get('theme.site')));
            $app['theme.site']->boot($app);

        } catch (\Exception $e) {}
    }

    /**
     * Sets the admin layout.
     */
    public function onSystemAdmin()
    {
        $this('view')->setLayout($this('theme.admin')->getLayout());
    }

    /**
     * Sets the site layout.
     */
    public function onSystemSite()
    {
        $this('view')->setLayout($this('theme.site')->getLayout());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.init'  => array('onSystemInit', 10),
            'system.admin' => 'onSystemAdmin',
            'system.site'  => 'onSystemSite'
        );
    }
}
