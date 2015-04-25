<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('app.request', function () use ($app) {
            $app['view']->addHelper(new EditorHelper());
            $app['scripts']->register('editor', 'app/system/modules/editor/app/editor.js', ['finder', 'uikit-htmleditor']);
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'templates' => [

        'image.replace' => 'app/system/modules/editor/views/image.replace.php',
        'link.replace'  => 'app/system/modules/editor/views/link.replace.php',
        'video.replace' => 'app/system/modules/editor/views/video.replace.php'

    ]

];
