<?php

namespace Pagekit\System;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Symfony\Component\Finder\Finder;

class SystemModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['system'] = $this;
        $app['isAdmin'] = false;

        $app->factory('finder', function () {
            return Finder::create();
        });

        $app->extend('assets', function ($factory) use ($app) {

            $secret = $this->config['secret'];
            $version = substr(sha1($app['version'] . $secret), 0, 4);
            $factory->setVersion($version);

            return $factory;

        });

        $theme = $this->config('site.theme');

        $app['module']->addLoader(function ($module) use ($app, $theme) {

            if (in_array($module['name'], $this->config['extensions'])) {

                $module['type'] = 'extension';

                $app['locator']->add("{$module['name']}:", $module['path']);
                $app['locator']->add("views:{$module['name']}", "{$module['path']}/views");

            } else if ($module['name'] == $theme) {

                $module['type'] = 'theme';

                $app['locator']->add('theme:', $module['path']);
                $app['locator']->add('views:', "{$module['path']}/views");
            }

            return $module;
        });

        foreach (array_merge($this->config['extensions'], (array) $theme) as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $module = ucfirst($module);
                $app['log']->error("[$module exception]: {$e->getMessage()}");
            }
        }

        if (!$app['theme'] = $app->module($theme)) {
            $app['theme'] = new Module([
                'name' => 'theme-default',
                'type' => 'theme',
                'path' => '',
                'config' => [],
                'layout' => 'views:system/blank.php'
            ]);
        }

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
