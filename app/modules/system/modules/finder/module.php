<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['view']->addHelper('finder', new FinderHelper);

            $app['scripts']->register('finder', 'app/modules/system/modules/finder/app/finder.js', ['uikit-upload', 'vue-system']);
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

        'finder.main'      => 'app/modules/system/modules/finder/views/main.php',
        'finder.table'     => 'app/modules/system/modules/finder/views/table.php',
        'finder.thumbnail' => 'app/modules/system/modules/finder/views/thumbnail.php'

    ]

];
