<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\Templating\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('system.loaded', function ($event) use ($app) {

            $helper = new EditorHelper($app['events']);

            $app['tmpl.php']->addHelpers([$helper]);
            $app['tmpl.razr']->addDirective(new FunctionDirective('editor', [$helper, 'render']));

        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ]

];
