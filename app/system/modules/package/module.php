<?php

return [

    'name' => 'system/package',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['scripts']->register('marketplace', 'app/system/modules/package/app/marketplace.js', 'vue-system');
        });

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

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
            'parent'   => 'system: settings',
            'url'      => '@system/extensions',
            'access'   => 'system: manage extensions'
        ],

        'system: themes' => [
            'label'    => 'Themes',
            'parent'   => 'system: settings',
            'url'      => '@system/themes',
            'access'   => 'system: manage themes'
        ]

    ],

    'templates' => [

        'marketplace.main' => 'app/system/modules/package/views/marketplace.php'

    ]

];
