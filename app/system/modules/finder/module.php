<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('app.request', function() use ($app) {

            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'system/finder:app/bundle/finder.js', ['vue', 'uikit-upload']);

        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'routes' => [

        '@system/finder' => [
            'path' => '/system/finder',
            'controller' => 'Pagekit\\Finder\\Controller\\FinderController'
        ],
        '@system/storage' => [
            'path' => '/system/storage',
            'controller' => 'Pagekit\\Finder\\Controller\\StorageController'
        ]

    ],

    'resources' => [

        'system/finder:' => ''

    ],

    'menu' => [

        'system: storage' => [
            'label'    => 'Storage',
            'parent'   => 'system: system',
            'url'      => '@system/storage',
            'access'   => 'system: manage storage',
            'priority' => 20
        ]

    ]

];
