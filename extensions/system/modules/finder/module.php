<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['view']->addHelpers(['finder' => new FinderHelper]);

            $app['scripts']->register('finder', 'extensions/system/modules/finder/app/finder.js', ['uikit-upload', 'vue-system']);
        });

        $app->on('system.tmpl', function ($event) {
            $event->register('finder.main', 'extensions/system/modules/finder/views/main.php');
            $event->register('finder.table', 'extensions/system/modules/finder/views/table.php');
            $event->register('finder.thumbnail', 'extensions/system/modules/finder/views/thumbnail.php');
        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Finder\\Controller\\FinderController'
        ],

    ],

    'config' => [

    ]

];
