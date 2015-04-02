<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['view']->addHelper(new FinderHelper());
            $app['scripts']->register('finder', 'app/system/modules/finder/app/finder.js', ['uikit-upload', 'vue-system']);
        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Finder\\Controller\\FinderController'
        ]

    ],

    'templates' => [

        'finder.main'      => 'app/system/modules/finder/views/main.php',
        'finder.table'     => 'app/system/modules/finder/views/table.php',
        'finder.thumbnail' => 'app/system/modules/finder/views/thumbnail.php'

    ]

];
