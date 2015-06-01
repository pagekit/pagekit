<?php

use Pagekit\System\ExtensionRepository;
use Pagekit\System\Package\PackageManager;
use Pagekit\System\ThemeRepository;

return [

    'name' => 'system/package',

    'main' => function ($app) {

        $app['package'] = function ($app) {
            return (new PackageManager)
                ->addPath($app['path.extensions'].'/*/extension.json')
                ->addPath($app['path.themes'].'/*/theme.json');
        };

        $app->on('app.request', function () use ($app) {

            $app['scripts']->register('v-marketplace', 'system/package:app/bundle/marketplace.js', 'vue');
            $app['scripts']->register('v-upload', 'system/package:app/bundle/upload.js', ['vue', 'uikit-upload']);

        }, 120);

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'routes' => [

        '@system/package' => [
            'path' => '/system/package',
            'controller' => 'Pagekit\\System\\Controller\\PackageController'
        ],
        '@system/marketplace' => [
            'path' => '/system/marketplace',
            'controller' => 'Pagekit\\System\\Controller\\MarketplaceController'
        ]

    ],

    'resources' => [

        'system/package:' => ''

    ],

    'permissions' => [

        'system: manage packages' => [
            'title' => 'Manage extensions and themes'
        ]

    ],

    'menu' => [

        'system: marketplace' => [
            'label'    => 'Marketplace',
            'icon'     => 'system/package:assets/images/icon-marketplace.svg',
            'url'      => '@system/marketplace/extensions',
            'priority' => 125
        ],

        'system: marketplace extensions' => [
            'label'    => 'Extensions',
            'parent'   => 'system: marketplace',
            'url'      => '@system/marketplace/extensions'
        ],

        'system: marketplace themes' => [
            'label'    => 'Themes',
            'parent'   => 'system: marketplace',
            'url'      => '@system/marketplace/themes'
        ],

        'system: extensions' => [
            'label'    => 'Extensions',
            'parent'   => 'system: system',
            'url'      => '@system/package/extensions',
            'access'   => 'system: manage packages',
            'priority' => 5
        ],

        'system: themes' => [
            'label'    => 'Themes',
            'parent'   => 'system: system',
            'url'      => '@system/package/themes',
            'access'   => 'system: manage packages',
            'priority' => 10
        ]

    ]

];
