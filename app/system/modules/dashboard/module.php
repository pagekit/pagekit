<?php

return [

    'name' => 'system/dashboard',

    'main' => 'Pagekit\\Dashboard\\DashboardModule',

    'autoload' => [

        'Pagekit\\Dashboard\\' => 'src'

    ],

    'resources' => [

        'system/dashboard:' => ''

    ],

    'controllers' => [

        '@dashboard: /' => [
            'Pagekit\\Dashboard\\Controller\\DashboardController'
        ]

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

        'default' => [

            '1' => [
                'id' => '1',
                'type' => 'widget.user'
            ]

        ]

    ]

];
