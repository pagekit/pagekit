<?php

return [

    'name' => 'system/user',

    'main' => 'Pagekit\\User\\UserModule',

    'autoload' => [

        'Pagekit\\User\\' => 'src'

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\User\\Controller\\AuthController',
            'Pagekit\\User\\Controller\\ProfileController'
        ],

        '@system: /system' => [
            'Pagekit\\User\\Controller\\PermissionController',
            'Pagekit\\User\\Controller\\RegistrationController',
            'Pagekit\\User\\Controller\\ResetPasswordController',
            'Pagekit\\User\\Controller\\RoleController',
            'Pagekit\\User\\Controller\\UserController'
        ]

    ],

    'menu' => [

        'system: user' => [
            'label'    => 'Users',
            'icon'     => 'extensions/system/assets/images/icon-users.svg',
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
        ]

    ],

    'permissions' => [

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
        ]

    ]

];
