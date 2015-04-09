<?php

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'controllers' => [

        '@site: /' => [
            'Pagekit\\Site\\Controller\\SiteController'
        ],

        '@site/api: /api/site' => [
            'Pagekit\\Site\\Controller\\MenuController',
            'Pagekit\\Site\\Controller\\NodeController'
        ]

    ],

    'menu' => [

        'site' => [
            'label'    => 'Site',
            'icon'     => 'site:assets/images/icon-site.svg',
            'url'      => '@site',
            'active'   => '@site*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'site: manage site' => [
            'title' => 'Manage site'
        ]

    ],

    'config' => [
        'menus'     => [],
        'frontpage' => false
    ],

    'resources' => [

        'site:' => ''

    ]

];
