<?php

use Pagekit\Editor\EditorHelper;

return [

    'name' => 'system/editor',

    'main' => function ($app) {

        $app->on('app.request', function () use ($app) {
            $app['view']->addHelper(new EditorHelper());
            $app['scripts']->register('editor', 'app/system/modules/editor/app/bundle/components/editor.js', ['uikit-htmleditor', 'finder']);
        });

    },

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'config' => [

        'editor' => 'htmleditor'

    ]

];
