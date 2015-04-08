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

        $app->on('system.init', function() use ($app) {
            $app['scripts']->register('marketplace', 'app/system/modules/package/app/marketplace.js', 'vue-system');
        });

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

    'templates' => [

        'marketplace.main' => 'system/package:views/marketplace.php',
        'package.upload'   => 'system/package:views/upload.php'

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
