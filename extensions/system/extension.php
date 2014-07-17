<?php

return [

    'main' => 'Pagekit\\SystemExtension',

    'controllers' => 'src/*/Controller/*Controller.php',

    'menu' => [

        'system: dashboard' => [
            'label'    => 'Dashboard',
            'icon'     => 'extension://system/assets/images/icon-dashboard.svg',
            'url'      => '@system/dashboard',
            'active'   => '@system/dashboard',
            'priority' => 0
        ],
        'system: widgets' => [
            'label'    => 'Widgets',
            'icon'     => 'extension://system/assets/images/icon-widgets.svg',
            'url'      => '@system/widgets',
            'active'   => '@system/widgets*',
            'access'   => 'system: manage widgets',
            'priority' => 5
        ],
        'system: menu' => [
            'label'    => 'Menus',
            'icon'     => 'extension://system/assets/images/icon-menus.svg',
            'url'      => '@system/menu',
            'active'   => '@system/(menu|item)*',
            'access'   => 'system: manage menus',
            'priority' => 10
        ],
        'system: user' => [
            'label'    => 'Users',
            'icon'     => 'extension://system/assets/images/icon-users.svg',
            'url'      => '@system/user',
            'active'   => '@system/user*',
            'access'   => 'system: manage users || system: manage user permissions',
            'priority' => 15
        ],
        'system: sub-user' => [
            'label'    => 'Users',
            'parent'   => 'system: user',
            'url'      => '@system/user',
            'active'   => '@system/user*',
            'access'   => 'system: manage users',
            'priority' => 15
        ],
        'system: user permissions' => [
            'label'    => 'Permissions',
            'parent'   => 'system: user',
            'url'      => '@system/permission',
            'active'   => '@system/permission*',
            'access'   => 'system: manage user permissions'
        ],
        'system: user roles' => [
            'label'    => 'Roles',
            'parent'   => 'system: user',
            'url'      => '@system/role',
            'active'   => '@system/role*',
            'access'   => 'system: manage user permissions'
        ],
        'system: settings' => [
            'label'    => 'Settings',
            'icon'     => 'extension://system/assets/images/icon-settings.svg',
            'url'      => '@system/system',
            'active'   => '@system/(system|settings|themes|extensions|storage|alias|update|info|marketplace|dashboard)*',
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
        'system: manage url aliases' => [
            'title' => 'Manage url aliases'
        ],
        'system: manage users' => [
            'title' => 'Manage users',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: manage user permissions' => [
            'title' => 'Manage user permissions',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: access admin area' => [
            'title' => 'Access admin area',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
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
