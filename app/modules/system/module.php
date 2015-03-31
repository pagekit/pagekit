<?php

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'require' => [

        'profiler',
        'application',
        'cache',
        'option',
        'locale',
        'comment',
        'dashboard',
        'feed',
        'mail',
        'markdown',
        'migration',
        'oauth',
        'package',
        'tree',
        'user',
        'system/console',
        'system/content',
        'system/editor',
        'system/finder',
        'system/marketplace',
        'system/menu',
        'system/site',
        'system/theme',
        'system/widget'

    ],

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'resources' => [

        'system:' => ''

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\System\\Controller\\AdminController'
        ],

        '@system: /system' => [
            'Pagekit\\System\\Controller\\ExtensionsController',
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
            'icon'     => 'app/modules/system/assets/images/icon-settings.svg',
            'url'      => '@system/system',
            'active'   => '(@system|@dashboard)/(system|settings|themes|extensions|storage|update|info|marketplace)*',
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

    'templates' => [

        'linkpicker.modal'    => 'system:views/tmpl/linkpicker.modal.razr',
        'linkpicker.replace'  => 'system:views/tmpl/linkpicker.replace.razr',
        'package.upload'      => 'system:views/admin/upload.php'

    ],

    'config' => [

        'extensions' => [],

        'version' => '0.8.8',

        'dashboard' => [

            'default' => [
                '1' => [
                    'type' => 'widget.user'
                ]
            ]

        ],

        'key' => '',
        'frontpage' => '',

        'site' => [
            'title' => '',
            'description' => ''
        ],

        'maintenance' => [
            'enabled' => false,
            'msg' => ''
        ],

        'api' => [
            'key' => '',
            'url' => 'http://pagekit.com/api',
        ],

        'release_channel' => 'stable',

        'storage' => '/storage',

        'theme.site' => 'alpha'

    ]

];
