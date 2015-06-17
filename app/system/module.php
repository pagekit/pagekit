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
        'system/widget',
        'system/widget-text'

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
        ]

    ],

    'resources' => [

        'system:' => ''

    ],

    'config' => [

        'key' => '',

        'site' => [
            'locale' => 'en_US',
            'theme' => 'alpha'
        ],

        'admin' => [
            'locale' => 'en_US'
        ],

        'timezone' => 'UTC',

        'extensions' => []

    ]

];
