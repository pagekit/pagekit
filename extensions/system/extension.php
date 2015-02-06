<?php

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemExtension',

    'require' => [

        'system/profiler',
        'system/core',
        'system/comment',
        'system/console',
        'system/content',
        'system/dashboard',
        'system/editor',
        'system/feed',
        'system/mail',
        'system/markdown',
        'system/menu',
        'system/migration',
        'system/oauth',
        'system/package',
        'system/site',
        'system/theme',
        'system/tree',
        'system/user',
        'system/widget'

    ],

    'include' => 'modules/*/module.php',

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\System\\Controller\\AdminController'
        ],

        '@system: /system' => [
            'Pagekit\\System\\Controller\\ExtensionsController',
            'Pagekit\\System\\Controller\\FinderController',
            'Pagekit\\System\\Controller\\LinkController',
            'Pagekit\\System\\Controller\\MarketplaceController',
            'Pagekit\\System\\Controller\\MigrationController',
            'Pagekit\\System\\Controller\\PackageController',
            'Pagekit\\System\\Controller\\SettingsController',
            'Pagekit\\System\\Controller\\UpdateController',
            'Pagekit\\System\\Controller\\SystemController',
            'Pagekit\\System\\Controller\\ThemesController'
        ]

    ],

    'menu' => [

        'system: settings' => [
            'label'    => 'Settings',
            'icon'     => 'extensions/system/assets/images/icon-settings.svg',
            'url'      => '@system/system',
            'active'   => '@system/(system|settings|themes|extensions|storage|update|info|marketplace|dashboard)*',
            'priority' => 110
        ]

    ],

    'permissions' => [

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

    'config' => [

        'dashboard' => [

            'default' => [
                '1' => [
                    'type' => 'widget.user'
                ]
            ]

        ],

        'frontpage' => '',
        'site_title' => '',
        'site_description' => '',
        'maintenance.enabled' => false,
        'maintenance.msg' => '',
        'api.key' => '',
        'release_channel' => 'stable',
        'storage' => '',
        'theme.site' => 'alpha'

    ]

];
