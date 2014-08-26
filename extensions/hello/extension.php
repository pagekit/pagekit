<?php

return [

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'controllers' => 'src/Controller/*Controller.php',

    'parameters' => [

        'settings' => [

            'view' => 'extension://hello/views/admin/settings.razr',
            'defaults' => [
                'message' => 'World'
            ]

        ]

    ],

    'menu' => [

        'hello' => [
            'label'  => 'Hello',
            'icon'   => 'extension://hello/extension.svg',
            'url'    => '@hello/hello',
            'active' => '@hello/hello*',
            'access' => 'hello: manage hellos'
        ]

    ]

];
