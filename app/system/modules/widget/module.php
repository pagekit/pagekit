<?php

return [

    'name' => 'system/widget',

    'main' => 'Pagekit\\Widget\\WidgetModule',

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'routes' => [

        '@widget' => [
            'path' => '/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetController'
        ],
        '@widget/api' => [
            'path' => '/api/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetApiController'
        ]

    ],

    'resources' => [

        'widget:' => ''

    ],

    'permissions' => [

        'system: manage widgets' => [
            'title' => 'Manage widgets'
        ]

    ],

    'menu' => [

        'site: widgets' => [
            'label'  => 'Widgets',
            'parent' => 'site',
            'url'    => '@widget'
        ]

    ],

    'config' => [

        'widget' => [

            'positions' => [],
            'config' => [],
            'defaults' => []

        ]

    ]

];
