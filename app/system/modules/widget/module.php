<?php

return [

    'name' => 'system/widget',

    'main' => 'Pagekit\\Widget\\WidgetModule',

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'routes' => [

        '@widget/api' => [
            'path' => '/api/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetController'
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

    'config' => [

        'widget' => [

            'positions' => [],
            'config' => [],
            'defaults' => []

        ]

    ]

];
