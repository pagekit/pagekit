<?php

use Pagekit\Finder\FinderHelper;

return [

    'name' => 'system/finder',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['view']->addHelper('finder', new FinderHelper);

            $app['scripts']->register('finder', 'extensions/system/modules/finder/app/finder.js', ['uikit-upload', 'vue-system']);
        });

        $app->on('system.loaded', function ($event) use ($app) {
            $app['view']->tmpl()->register('finder.main', 'extensions/system/modules/finder/views/main.php');
            $app['view']->tmpl()->register('finder.table', 'extensions/system/modules/finder/views/table.php');
            $app['view']->tmpl()->register('finder.thumbnail', 'extensions/system/modules/finder/views/thumbnail.php');
        });

    },

    'autoload' => [

        'Pagekit\\Finder\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Finder\\Controller\\FinderController'
        ]

    ]

];
