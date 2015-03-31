<?php

return [

    'name' => 'dashboard',

    'main' => 'Pagekit\\Dashboard\\DashboardModule',

    'autoload' => [

        'Pagekit\\Dashboard\\' => 'src'

    ],

    'resources' => [

        'dashboard:' => ''

    ],

    'controllers' => [

        '@dashboard: /' => [
            'Pagekit\\Dashboard\\Controller\\DashboardController'
        ]

    ],

    'menu' => [

        'dashboard' => [
            'label'    => 'Dashboard',
            'icon'     => 'app/modules/dashboard/assets/images/icon-dashboard.svg',
            'url'      => '@dashboard',
            'active'   => '@dashboard',
            'priority' => 0
        ]

    ]

];
