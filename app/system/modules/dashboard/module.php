<?php

return [

    'name' => 'system/dashboard',

    'main' => 'Pagekit\\Dashboard\\DashboardModule',

    'autoload' => [

        'Pagekit\\Dashboard\\' => 'src'

    ],

    'routes' => [

        '@dashboard' => [
            'path' => '/dashboard',
            'controller' => 'Pagekit\\Dashboard\\Controller\\DashboardController'
        ]

    ],

    'resources' => [

        'system/dashboard:' => ''

    ],

    'menu' => [

        'dashboard' => [
            'label'    => 'Dashboard',
            'icon'     => 'system/dashboard:assets/images/icon-dashboard.svg',
            'url'      => '@dashboard',
            'active'   => '@dashboard*',
            'priority' => 0
        ]

    ],

    'config' => [

        'defaults' => []

    ]

];
