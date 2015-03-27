<?php

use Pagekit\Editor\Editor;
use Pagekit\Editor\EditorHelper;
use Razr\Directive\FunctionDirective;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->subscribe(new Editor);

        $app->on('system.loaded', function ($event) use ($app) {

            $helper = new EditorHelper();

            $app['view']->addHelper('editor', $helper);
            $app['scripts']->register('editor', 'extensions/system/modules/editor/app/editor.js', ['uikit-htmleditor', 'finder']);
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'templates' => [

        'image.modal'   => 'extensions/system/modules/editor/views/image.modal.php',
        'image.replace' => 'extensions/system/modules/editor/views/image.replace.php',
        'link.modal'    => 'extensions/system/modules/editor/views/link.modal.php',
        'link.replace'  => 'extensions/system/modules/editor/views/link.replace.php',
        'video.modal'   => 'extensions/system/modules/editor/views/video.modal.php',
        'video.replace' => 'extensions/system/modules/editor/views/video.replace.php'

    ]

];
