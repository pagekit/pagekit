<?php

return [

    'name' => 'system/widget',

    'main' => 'Pagekit\\Widget\\WidgetModule',

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'resources' => [

        'widget:' => ''

    ],

    'controllers' => [

        '@widget: /' => [
            'Pagekit\\Widget\\Controller\\WidgetsController'
        ],

        '@widget/api: /api/widget' => [
            'Pagekit\\Widget\\Controller\\WidgetsApiController'
        ]

    ],

    'permissions' => [

        'system: manage widgets' => [
            'title' => 'Manage widgets'
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
