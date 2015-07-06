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

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('editor', 'app/system/modules/editor/app/bundle/editor.js', ['uikit-htmleditor', 'finder', 'v-linkpicker']);
        }

    ]

];
