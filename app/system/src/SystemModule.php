<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Kernel\Event\ExceptionListener;
use Pagekit\Module\Module;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Migration\FilesystemLoader;
use Symfony\Component\Finder\Finder;

class SystemModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['system'] = $this;

        $app->factory('finder', function () {
            return Finder::create();
        });

        $app['module']['auth']->config['rememberme.key'] = $this->config('key');

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');

        $app['db.em']; // -TODO- fix me

        // TODO access "view" to early
        $app['view']->on('messages', function ($event) use ($app) {

            $result = '';

            if ($app['message']->peekAll()) {
                foreach ($app['message']->levels() as $level) {
                    if ($messages = $app['message']->get($level)) {
                        foreach ($messages as $message) {
                            $result .= sprintf('<div class="uk-alert uk-alert-%1$s" data-status="%1$s">%2$s</div>', $level == 'error' ? 'danger' : $level, $message);
                        }
                    }
                }
            }

            if ($result) {
                $event->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
            }

        });

        foreach ($this->config['extensions'] as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $app['log']->warn("Unable to load extension: $module");
            }
        }

        $app['module']->load($theme = $this->config('site.theme'));

        if ($app['theme.site'] = $app['module']->get($theme)) {
            $app->on('app.site', function () use ($app) {
                $app['view']->map('layout', $app['theme.site']->getLayout());
            });
        }

        $app->extend('migrator', function($migrator) {
            $migrator->setLoader(new FilesystemLoader());
            return $migrator;
        });
    }

    public function boot($app)
    {
        if (!$app['debug']) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Controller\ExceptionController::showAction'));
        }

        $app->subscribe(
            new MaintenanceListener,
            new MigrationListener,
            new SystemListener
        );

        if ($app->inConsole()) {
            $app['isAdmin'] = false;
        }

        $app->on('app.request', function ($event, $request) use ($app) {

            if (!$event->isMasterRequest()) {
                return;
            }

            $app['isAdmin'] = $admin = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());
            $app['intl']->setDefaultLocale($this->config($admin ? 'admin.locale' : 'site.locale'));

        }, 50);

    }

    /**
     * Gets the system menu.
     *
     * @return array
     */
    public function getMenu()
    {
        static $menu;

        if (!$menu) {

            $menu = new SystemMenu();

            foreach (App::module() as $module) {

                if (!isset($module->menu)) {
                    continue;
                }

                foreach ($module->menu as $id => $item) {
                    $menu->addItem($id, $item);
                }
            }
        }

        return $menu;
    }
}
