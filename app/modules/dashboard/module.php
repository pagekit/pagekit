<?php

return [

    'name' => 'system/dashboard',

    'main' => 'Pagekit\\Dashboard\\DashboardModule',

    'autoload' => [

        'Pagekit\\Dashboard\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Dashboard\\Controller\\DashboardController'
        ]

    ],

    'menu' => [

        'system: dashboard' => [
            'label'    => 'Dashboard',
            'icon'     => 'app/modules/system/assets/images/icon-dashboard.svg',
            'url'      => '@system/dashboard',
            'active'   => '@system/dashboard',
            'priority' => 0
        ]

    ]

];
