<?php

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'routes' => [

        '/' => [
            'name' => '@site',
            'controller' => 'Pagekit\\Site\\Controller\\SiteController'
        ],
        '/api/site/menu' => [
            'name' => '@site/api/menu',
            'controller' => 'Pagekit\\Site\\Controller\\MenuController'
        ],
        '/api/site/node' => [
            'name' => '@site/api/node',
            'controller' => 'Pagekit\\Site\\Controller\\NodeController'
        ]

    ],

    'resources' => [

        'site:' => ''

    ],

    'permissions' => [

        'site: manage site' => [
            'title' => 'Manage site'
        ],
        'site: maintenance access' => [
            'title' => 'Use the site in maintenance mode'
        ]

    ],

    'menu' => [

        'site' => [
            'label' => 'Site',
            'icon' => 'site:assets/images/icon-site.svg',
            'url' => '@site',
            'active' => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label' => 'Pages',
            'parent' => 'site',
            'url' => '@site'
        ],
        'site: settings' => [
            'label' => 'Settings',
            'parent' => 'site',
            'url' => '@site/settings',
            'priority' => 5
        ]

    ],

    'config' => [

        'menus' => [],
        'frontpage' => 0,

        'title' => '',
        'description' => '',

        'maintenance' => [
            'enabled' => false,
            'msg' => ''
        ]

    ]

];
