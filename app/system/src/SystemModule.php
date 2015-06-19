<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
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

        $app['db.em']; // -TODO- fix me

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
                foreach ((array) $module->get('menu') as $id => $item) {
                    $menu->addItem($id, $item);
                }
            }
        }

        return $menu;
    }
}
