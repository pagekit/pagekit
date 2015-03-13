<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['view']->addHelper('finder', new FinderHelper);

            $app['scripts']->register('finder', 'extensions/system/modules/finder/app/finder.js', ['uikit-upload', 'vue-system']);
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

        'finder.main'      => 'extensions/system/modules/finder/views/main.php',
        'finder.table'     => 'extensions/system/modules/finder/views/table.php',
        'finder.thumbnail' => 'extensions/system/modules/finder/views/thumbnail.php'

    ]

];
