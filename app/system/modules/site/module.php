<?php

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'routes' => [

        '@site' => [
            'path' => '/',
            'controller' => 'Pagekit\\Site\\Controller\\SiteController'
        ],
        '@site/api/menu' => [
            'path' => '/api/site/menu',
            'controller' => 'Pagekit\\Site\\Controller\\MenuController'
        ],
        '@site/api/node' => [
            'path' => '/api/site/node',
            'controller' => 'Pagekit\\Site\\Controller\\NodeController'
        ]

    ],

    'resources' => [

        'site:' => ''

    ],

    'permissions' => [

        'site: manage site' => [
            'title' => 'Manage site'
        ]

    ],

    'menu' => [

        'site' => [
            'label'    => 'Site',
            'icon'     => 'site:assets/images/icon-site.svg',
            'url'      => '@site',
            'active'   => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label'    => 'Pages',
            'parent'   => 'site',
            'url'      => '@site'
        ],
        'site: settings' => [
            'label'    => 'Settings',
            'parent'   => 'site',
            'url'      => '@site/settings',
            'priority' => 5
        ]

    ],

    'config' => [

        'menus' => []

    ]

];
