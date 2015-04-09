<?php

return [

    'name' => 'system/settings',

    'main' => function ($app) {

    },

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\System\\Controller\\SettingsController'
        ]

    ],

    'menu' => [

        'system: system' => [
            'label'    => 'System',
            'icon'     => 'app/system/assets/images/icon-settings.svg',
            'url'      => '@system/settings',
            'priority' => 110
        ],

        'system: settings' => [
            'label'    => 'Settings',
            'parent'   => 'system: system',
            'url'      => '@system/settings',
            'priority' => 120
        ]

    ]

];
