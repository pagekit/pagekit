<?php

return [

    'name' => 'system/editor',

    'autoload' => [

        'Pagekit\\Editor\\' => 'src'

    ],

    'config' => [

        'editor' => 'html'

    ],

    'resources' => [

        'system/editor:' => ''

    ],

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('editor', 'system/editor:app/bundle/editor.js', ['input-link']);
        }

    ]

];
