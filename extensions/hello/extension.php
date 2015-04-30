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

        // 'settings.view' => 'hello:views/admin/settings.razr',
        'default' => 'World'

    ],

    'menu' => [

        'hello' => [
            'label'  => 'Hello',
            'icon'   => 'extensions/hello/extension.svg',
            'url'    => '@hello',
            // 'access' => 'hello: manage hellos'
        ],

        'hello: index' => [
            'label'  => 'Hello',
            'icon'   => 'extensions/hello/extension.svg',
            'url'    => '@hello',
            'parent' => 'hello'
            // 'access' => 'hello: manage hellos'
        ],

        'hello: settings' => [
            'label'  => 'Settings',
            'url'    => '@hello/settings',
            'parent' => 'hello',
            // 'access' => 'hello: manage hellos'
        ]


    ]

];
