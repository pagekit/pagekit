<?php

use Pagekit\Module\Factory\ModuleFactory;
use Pagekit\Module\Loader\ConfigLoader;
use Pagekit\System\Package\PackageManager;

return [

    'name' => 'system/package',

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'main' => function ($app) {

        $app['package'] = function ($app) {
            return (new PackageManager())
                ->addPath($app['path.extensions'].'/*/extension.json')
                ->addPath($app['path.themes'].'/*/theme.json');
        };


        $app['module']->addLoader(function ($name, $module) use ($app) {

            if (isset($module['type']) && $module['type'] == 'extension') {
                $app['locator']->add("{$module['name']}:", $module['path']);
                $app['locator']->add("views:{$module['name']}", "{$module['path']}/views");
            }

            return $module;
        });

        $app['module']->addLoader(new ConfigLoader($app['config']->get('theme')->toArray()));
        $app['module']->addFactory('theme', new ModuleFactory($app, 'Pagekit\System\Theme'));
        $app['module']->addFactory('extension', new ModuleFactory($app));

    },

    'events' => [

        'view.system:modules/settings/views/settings' => function ($event, $view) use ($app) {
            $view->data('$settings', ['options' => [$this->name => $this->config]]);
        }

    ],

    'routes' => [

        '/system/package' => [
            'name' => '@system/package',
            'controller' => 'Pagekit\\System\\Controller\\PackageController'
        ],
        '/system/marketplace' => [
            'name' => '@system/marketplace',
            'controller' => 'Pagekit\\System\\Controller\\MarketplaceController'
        ],
        '/system/update' => [
            'name' => '@system/update',
            'controller' => 'Pagekit\\System\\Controller\\UpdateController'
        ]

    ],

    'resources' => [

        'system/package:' => ''

    ],

    'permissions' => [

        'system: manage packages' => [
            'title' => 'Manage extensions and themes',
            'description' => 'Manage extensions and themes'
        ],
        'system: software updates' => [
            'title' => 'Apply system updates',
            'trusted' => true
        ]

    ],

    'menu' => [

        'system: marketplace' => [
            'label' => 'Marketplace',
            'icon' => 'system/package:assets/images/icon-marketplace.svg',
            'url' => '@system/marketplace/extensions',
            'access' => 'system: manage packages',
            'priority' => 125
        ],

        'system: marketplace extensions' => [
            'label' => 'Extensions',
            'parent' => 'system: marketplace',
            'url' => '@system/marketplace/extensions'
        ],

        'system: marketplace themes' => [
            'label' => 'Themes',
            'parent' => 'system: marketplace',
            'url' => '@system/marketplace/themes'
        ],

        'system: extensions' => [
            'label' => 'Extensions',
            'parent' => 'system: system',
            'url' => '@system/package/extensions',
            'access' => 'system: manage packages',
            'priority' => 5
        ],

        'system: themes' => [
            'label' => 'Themes',
            'parent' => 'system: system',
            'url' => '@system/package/themes',
            'access' => 'system: manage packages',
            'priority' => 10
        ],

        'system: update' => [
            'label' => 'Update',
            'parent' => 'system: system',
            'url' => '@system/update',
            'priority' => 25
        ]

    ],

    'config' => [

        'api' => [
            'key' => '',
            'url' => 'http://pagekit.com/api'
        ],

        'release_channel' => 'stable'

    ]

];
