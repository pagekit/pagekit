<?php

return [

    'name' => 'hello',

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'routes' => [

        '@hello' => [
            'path' => '/',
            'controller' => [
                'Pagekit\\Hello\\Controller\\HelloController',
                'Pagekit\\Hello\\Controller\\SiteController'
            ]
        ]

    ],

    'resources' => [

        'hello:' => ''

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

    ],

    'config' => [

        'default' => 'World'

    ]

];
