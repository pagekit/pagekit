<?php

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'require' => [

        'application',
        'feed',
        'markdown',
        'migration',
        'package',
        'tree',
        'system/view',
        'system/cache',
        'system/comment',
        'system/console',
        'system/content',
        'system/dashboard',
        'system/editor',
        'system/finder',
        'system/info',
        'system/mail',
        'system/menu',
        'system/package',
        'system/page',
        'system/settings',
        'system/site',
        'system/theme',
        'system/user',
        'system/widget'

    ],

    'include' => 'modules/*/module.php',

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'routes' => [

        '/' => [
            'name' => '@system',
            'controller' => 'Pagekit\\System\\Controller\\AdminController'
        ],
        '/system/intl' => [
            'name' => '@system/intl',
            'controller' => 'Pagekit\\System\\Controller\\IntlController'
        ],
        '/system/migration' => [
            'name' => '@system/migration',
            'controller' => 'Pagekit\\System\\Controller\\MigrationController'
        ],
        '/system/update' => [
            'name' => '@system/update',
            'controller' => 'Pagekit\\System\\Controller\\UpdateController'
        ]

    ],

    'resources' => [

        'system:' => ''

    ],

    'permissions' => [

        'system: access settings' => [
            'title' => 'Access system settings',
            'trusted' => true
        ],
        'system: software updates' => [
            'title' => 'Apply system updates',
            'trusted' => true
        ],
        'system: manage storage' => [
            'title' => 'Manage storage',
            'trusted' => true
        ],
        'system: manage storage read only' => [
            'title' => 'Manage storage (Read only)'
        ],
        'system: maintenance access' => [
            'title' => 'Use the site in maintenance mode'
        ]

    ],

    'config' => [

        'key' => '',

        'api' => [
            'key' => '',
            'url' => 'http://pagekit.com/api',
        ],

        'site' => [
            'title' => '',
            'description' => '',
            'locale' => 'en_US',
            'theme' => 'alpha'
        ],

        'admin' => [
            'locale' => 'en_US'
        ],

        'maintenance' => [
            'enabled' => false,
            'msg' => ''
        ],

        'timezone' => 'UTC',

        'storage' => '/storage',

        'extensions' => [],

        'release_channel' => 'stable'

    ]

];
