<?php

return [

    'name' => 'hello',

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'resources' => [

        'hello:' => ''

    ],

    'controllers' => [

        '@hello: /' => [
            'Pagekit\\Hello\\Controller\\HelloController',
            'Pagekit\\Hello\\Controller\\SiteController'
        ]
    ],

    'config' => [

        'settings.view' => 'hello:views/admin/settings.razr',
        'message' => 'World'

    ],

    'menu' => [

        'hello' => [
            'label'  => 'Hello',
            'icon'   => 'extensions/hello/extension.svg',
            'url'    => '@hello',
            'active' => '@hello*',
            // 'access' => 'hello: manage hellos'
        ],

        'hello: settings' => [
            'label'  => 'Settings',
            'url'    => '@hello/settings',
            'parent' => '@hello',
            // 'access' => 'hello: manage hellos'
        ]


    ]

];
