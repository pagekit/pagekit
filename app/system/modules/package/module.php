<?php

use Pagekit\System\Package\PackageManager;
use Pagekit\System\Package\Repository\ExtensionRepository;
use Pagekit\System\Package\Repository\ThemeRepository;

return [

    'name' => 'system/package',

    'main' => function ($app) {

        $app['package'] = function ($app) {
            return (new PackageManager)
                ->addRepository('extension', new ExtensionRepository($app['path.extensions']))
                ->addRepository('theme', new ThemeRepository($app['path.themes']));
        };

        $app->on('app.request', function () use ($app) {

            $app['scripts']->register('marketplace', 'system/package:assets/components/marketplace.js', 'vue-system');
            $app['scripts']->register('upload', 'system/package:assets/components/upload.js', ['vue-system', 'uikit-upload']);

            $app['module']->load($theme = $app['system']->config('site.theme'));

            if ($app['theme.site'] = $app['module']->get($theme)) {
                $app->on('app.site', function () use ($app) {
                    $app['view']->map('layout', $app['theme.site']->getLayout());
                });
            }

        }, 120);

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'resources' => [

        'system/package:' => ''

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\System\\Controller\\PackageController',
            'Pagekit\\System\\Controller\\ExtensionsController',
            'Pagekit\\System\\Controller\\ThemesController'
        ]

    ],

    'menu' => [

        'system: extensions' => [
            'label'    => 'Extensions',
            'parent'   => 'system: system',
            'url'      => '@system/extensions',
            'access'   => 'system: manage extensions',
            'priority' => 130
        ],

        'system: themes' => [
            'label'    => 'Themes',
            'parent'   => 'system: system',
            'url'      => '@system/themes',
            'access'   => 'system: manage themes',
            'priority' => 130
        ]

    ]

];
