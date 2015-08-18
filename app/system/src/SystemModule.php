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

        $app->module('auth')->config['rememberme.key'] = $this->config('key');

        $app['db.em']; // -TODO- fix me

        $theme = $this->config('site.theme');

        foreach (array_merge($this->config['extensions'], (array) $theme) as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $app['log']->warn("Unable to load extension: $module");
            }
        }

        if (!$app['theme'] = $app->module($theme)) {
            $app['theme'] = new Module([
                'name' => 'default-theme',
                'path' => '',
                'config' => [],
                'layout' => 'views:system/blank.php'
            ]);
        }

        $app->extend('view', function ($view) use ($app) {

            $theme = $app->isAdmin() ? $app['module']['system/theme'] : $app['theme'];

            $view->map('layout', $theme->get('layout', 'views:template.php'));

            return $view->addGlobal('theme', $app['theme']);
        });

        $app->extend('migrator', function ($migrator) {
            return $migrator->setLoader(new FilesystemLoader());
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
