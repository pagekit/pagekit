<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Filesystem\Adapter\FileAdapter;
use Pagekit\Filesystem\Adapter\StreamAdapter;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Event\ThemeListener;
use Pagekit\System\Event\WidgetListener as ThemeWidgetListener;
use Pagekit\System\Helper\SystemInfoHelper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        if (!$app['module']['application']->config('debug')) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Exception\ExceptionController::showAction'));
        }

        $app->subscribe(
            new CanonicalListener,
            new FrontpageListener,
            new MaintenanceListener,
            new MigrationListener,
            new SystemListener,
            new ThemeListener,
            new ThemeWidgetListener
        );

        $app['version'] = function() {
            return $this->config['version'];
        };

        $app->factory('finder', function() {
            return Finder::create();
        });

        $app['module']['auth']->config['rememberme.key'] = $this->config('key');

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');

        $app['db.em']; // -TODO- fix me

        $app['system'] = $this;

        $app['systemInfo'] = function() {
            return new SystemInfoHelper;
        };

        $app->extend('assets', function ($assets) use ($app) {
            return $assets->register('file', 'Pagekit\System\Asset\FileAsset');
        });

        $app->on('kernel.boot', function() use ($app) {

            $app['module']->load($this->config['extensions']);

            if ($app->inConsole()) {
                $app['isAdmin'] = false;
                $app->trigger('system.init');
            }

        });

        $app->on('kernel.request', function($event) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $request = $event->getRequest();
            $baseUrl = $request->getSchemeAndHttpHost().$request->getBaseUrl();

            $app['file']->registerAdapter('file', new FileAdapter($app['path'], $baseUrl));
            $app['file']->registerAdapter('app', new StreamAdapter($app['path'], $baseUrl));

            $app['view']->meta(['generator' => 'Pagekit '.$app['version']]);

            $app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());

            $app->trigger('system.init', $event);

        }, 50);

        $app->on('kernel.request', function($event) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $app->trigger('system.loaded', $event);

        });

        $app->on('system.loaded', function () use ($app) {
            foreach ($app['module'] as $module) {

                if (!isset($module->resources)) {
                    continue;
                }

                foreach ($module->resources as $prefix => $path) {
                    $app['locator']->add($prefix, "$module->path/$path");
                }
            }
        });
    }

    /**
     * @{inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('app/modules/system/migrations', App::option('system:version'))->run()) {
            App::option()->set('system:version', $version);
        }

        foreach (['blog', 'page'] as $extension) {
            if ($extension = App::module($extension)) {
                $extension->enable();
            }
        }
    }
}
