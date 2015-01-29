<?php

use Pagekit\Dashboard\FeedWidget;
use Pagekit\Dashboard\WeatherWidget;

return [

    'name' => 'system/dashboard',

    'main' => function ($app, $config) {

        $app['system']->loadControllers($config);

        $app->on('system.admin_menu', function ($event) use ($config) {
            $event->register($config['menu']);
        });

        $app->on('system.dashboard', function ($event) {
            $event->register(new FeedWidget);
            $event->register(new WeatherWidget);
        });

    },

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
            'icon'     => 'extensions/system/assets/images/icon-dashboard.svg',
            'url'      => '@system/dashboard',
            'active'   => '@system/dashboard',
            'priority' => 0
        ]

    ]

];
