<?php

return [

    'main' => 'Pagekit\\SystemExtension',

    'include' => __DIR__.'/modules/*/module.php',

    'controllers' => [

        '@system: /' => [
            'Pagekit\\System\\Controller\\AdminController',
            'Pagekit\\System\\Controller\\OAuthController'
        ],

        '@system: /system' => [
            'Pagekit\\Extension\\Controller\\ExtensionsController',
            'Pagekit\\System\\Controller\\DashboardController',
            'Pagekit\\System\\Controller\\FinderController',
            'Pagekit\\System\\Controller\\LinkController',
            'Pagekit\\System\\Controller\\MarketplaceController',
            'Pagekit\\System\\Controller\\MigrationController',
            'Pagekit\\System\\Controller\\PackageController',
            'Pagekit\\System\\Controller\\SettingsController',
            'Pagekit\\System\\Controller\\UpdateController',
            'Pagekit\\System\\Controller\\SystemController',
            'Pagekit\\Theme\\Controller\\ThemesController',
            'Pagekit\\Widget\\Controller\\WidgetsController'
        ]

    ],

    'menu' => [

        'system: dashboard' => [
            'label'    => 'Dashboard',
            'icon'     => 'extensions/system/assets/images/icon-dashboard.svg',
            'url'      => '@system/dashboard',
            'active'   => '@system/dashboard',
            'priority' => 0
        ],
        'system: widgets' => [
            'label'    => 'Widgets',
            'icon'     => 'extensions/system/assets/images/icon-widgets.svg',
            'url'      => '@system/widgets',
            'active'   => '@system/widgets*',
            'access'   => 'system: manage widgets',
            'priority' => 5
        ],
        'system: menu' => [
            'label'    => 'Menus',
            'icon'     => 'extensions/system/assets/images/icon-menus.svg',
            'url'      => '@system/menu',
            'active'   => '@system/(menu|item)*',
            'access'   => 'system: manage menus',
            'priority' => 10
        ],
        'system: settings' => [
            'label'    => 'Settings',
            'icon'     => 'extensions/system/assets/images/icon-settings.svg',
            'url'      => '@system/system',
            'active'   => '@system/(system|settings|themes|extensions|storage|update|info|marketplace|dashboard)*',
            'priority' => 110
        ]

    ],

    'permissions' => [

        'system: manage menus' => [
            'title' => 'Manage menus'
        ],
        'system: manage widgets' => [
            'title' => 'Manage widgets'
        ],
        'system: manage themes' => [
            'title' => 'Manage themes'
        ],
        'system: manage extensions' => [
            'title' => 'Manage extensions'
        ],
        'system: access settings' => [
            'title' => 'Access system settings',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: software updates' => [
            'title' => 'Apply system updates',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: manage storage' => [
            'title' => 'Manage storage',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: manage storage read only' => [
            'title' => 'Manage storage (Read only)'
        ],
        'system: maintenance access' => [
            'title' => 'Use the site in maintenance mode'
        ]

    ],

    'dashboard' => [

        'default' => [
            '1' => [
                'type' => 'widget.user'
            ]
        ]

    ]

];
