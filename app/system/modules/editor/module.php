<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('system.loaded', function () use ($app) {
            $app['view']->addHelper(new EditorHelper());
            $app['scripts']->register('editor', 'app/system/modules/editor/app/editor.js', ['uikit-htmleditor', 'finder']);
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'templates' => [

        'image.modal'   => 'app/system/modules/editor/views/image.modal.php',
        'image.replace' => 'app/system/modules/editor/views/image.replace.php',
        'link.modal'    => 'app/system/modules/editor/views/link.modal.php',
        'link.replace'  => 'app/system/modules/editor/views/link.replace.php',
        'video.modal'   => 'app/system/modules/editor/views/video.modal.php',
        'video.replace' => 'app/system/modules/editor/views/video.replace.php'

    ]

];
