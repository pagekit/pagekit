<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('app.request', function() use ($app) {

            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'app/system/modules/finder/app/finder.js', ['vue-system', 'uikit-upload']);
            $app['scripts']->register('finder-main', 'app/system/modules/finder/views/main.php', '~finder', 'template');
            $app['scripts']->register('finder-table', 'app/system/modules/finder/views/table.php', '~finder', 'template');
            $app['scripts']->register('finder-thumbnail', 'app/system/modules/finder/views/thumbnail.php', '~finder', 'template');

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
