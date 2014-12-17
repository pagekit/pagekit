<?php

return [

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'controllers' => [

        '/hello' => [
            'Pagekit\\Hello\\Controller\\HelloController',
            'Pagekit\\Hello\\Controller\\SiteController'
        ]
    ],

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
