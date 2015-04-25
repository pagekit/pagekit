<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('app.request', function() use ($app) {

            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'system/finder:app/components/finder.js', ['vue-system', 'uikit-upload']);

        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'resources' => [

        'system/finder:' => ''

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Finder\\Controller\\FinderController',
            'Pagekit\\Finder\\Controller\\StorageController'
        ]

    ],

    'menu' => [

        'system: storage' => [
            'label'    => 'Storage',
            'parent'   => 'system: system',
            'url'      => '@system/storage',
            'access'   => 'system: manage storage',
            'priority' => 140
        ]

    ]

];
