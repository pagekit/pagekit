<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {

            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'app/system/modules/finder/app/finder.js', ['vue-system', 'uikit-upload', 'finder-main']);
            $app['scripts']->register('finder-main', 'app/system/modules/finder/views/main.php', ['finder-table', 'finder-thumbnail'], 'template');
            $app['scripts']->register('finder-table', 'app/system/modules/finder/views/table.php', [], 'template');
            $app['scripts']->register('finder-thumbnail', 'app/system/modules/finder/views/thumbnail.php', [], 'template');

        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

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
