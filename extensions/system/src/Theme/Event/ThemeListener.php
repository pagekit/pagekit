<?php

namespace Pagekit\Theme\Event;

use Pagekit\Package\Installer\PackageInstaller;
use Pagekit\Framework\Application as App;
use Pagekit\Theme\Package\ThemeLoader;
use Pagekit\Theme\Package\ThemeRepository;
use Pagekit\Theme\ThemeManager;
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

            $app['themes'] = function ($app) {

                $loader     = new ThemeLoader;
                $repository = new ThemeRepository($app['config']['theme.path'], $loader);
                $installer  = new PackageInstaller($repository, $loader);
                $file       = isset($app['file']) ? $app['file'] : null;

                return new ThemeManager($repository, $installer, $file);
            };

            $app['theme.admin'] = $app['themes']->load('system', 'extensions/system/theme');
            $app['theme.admin']->boot($app);

            $app['theme.site'] = $app['themes']->load($app['config']->get('theme.site'));
            $app['theme.site']->boot($app);

        } catch (\Exception $e) {}
    }

    /**
     * Sets the admin layout.
     */
    public function onSystemAdmin()
    {
        App::view()->setLayout(App::get('theme.admin')->getLayout());
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
            'system.admin' => 'onSystemAdmin',
            'system.site'  => 'onSystemSite'
        ];
    }
}
