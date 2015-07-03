<?php

return [

    'name' => 'system/editor',

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'config' => [

        'editor' => 'htmleditor'

    ],

    'events' => [

        'view.head' => [function () use ($app) {
            $app['scripts']->register('editor', 'app/system/modules/editor/app/bundle/editor.js', ['uikit-htmleditor', 'finder']);
        }, 50]

    ]

];
