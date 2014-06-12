<?php

return array(

    'main' => 'Pagekit\\SystemExtension',

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/*/Controller/*Controller.php',

    'menu' => array(

        'system: dashboard' => array(
            'label'    => 'Dashboard',
            'icon'     => 'asset://system/images/icon-settings-dashboard.svg',
            'url'      => '@system/dashboard/index',
            'active'   => '@system/dashboard/index',
            'priority' => 0
        ),
        'system: widgets' => array(
            'label'    => 'Widgets',
            'icon'     => 'asset://system/images/icon-widgets.svg',
            'url'      => '@system/widgets/index',
            'active'   => '@system/widgets*',
            'access'   => 'system: manage widgets',
            'priority' => 5
        ),
        'system: menu' => array(
            'label'    => 'Menus',
            'icon'     => 'asset://system/images/icon-menus.svg',
            'url'      => '@system/menu/index',
            'active'   => '@system/(menu|item)*',
            'access'   => 'system: manage menus',
            'priority' => 10
        ),
        'system: user' => array(
            'label'    => 'Users',
            'icon'     => 'asset://system/images/icon-users.svg',
            'url'      => '@system/user/index',
            'active'   => '@system/user*',
            'access'   => 'system: manage users || system: manage user permissions',
            'priority' => 15
        ),
        'system: sub-user' => array(
            'label'    => 'Users',
            'parent'   => 'system: user',
            'url'      => '@system/user/index',
            'active'   => '@system/user*',
            'access'   => 'system: manage users',
            'priority' => 15
        ),
        'system: user permissions' => array(
            'label'    => 'Permissions',
            'parent'   => 'system: user',
            'url'      => '@system/permission/index',
            'active'   => '@system/permission*',
            'access'   => 'system: manage user permissions'
        ),
        'system: user roles' => array(
            'label'    => 'Roles',
            'parent'   => 'system: user',
            'url'      => '@system/role/index',
            'active'   => '@system/role*',
            'access'   => 'system: manage user permissions'
        ),
        'system: settings' => array(
            'label'    => 'Settings',
            'icon'     => 'asset://system/images/icon-settings-system.svg',
            'url'      => '@system/system/index',
            'active'   => '@system/(system|settings|themes|extensions|storage|alias|update|info|marketplace|dashboard)*',
            'priority' => 110
        )

    ),

    'permissions' => array(

        'system: manage menus' => array(
            'title' => 'Manage menus'
        ),
        'system: manage widgets' => array(
            'title' => 'Manage widgets'
        ),
        'system: manage themes' => array(
            'title' => 'Manage themes'
        ),
        'system: manage extensions' => array(
            'title' => 'Manage extensions'
        ),
        'system: manage url aliases' => array(
            'title' => 'Manage url aliases'
        ),
        'system: manage users' => array(
            'title' => 'Manage users',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: manage user permissions' => array(
            'title' => 'Manage user permissions',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: access admin area' => array(
            'title' => 'Access admin area',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: access settings' => array(
            'title' => 'Access system settings',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: software updates' => array(
            'title' => 'Apply system updates',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: manage storage' => array(
            'title' => 'Manage storage',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ),
        'system: manage storage read only' => array(
            'title' => 'Manage storage (Read only)'
        ),
        'system: maintenance access' => array(
            'title' => 'Use the site in maintenance mode'
        )

    ),

    'dashboard' => array(

        'default' => array(
            '1' => array(
                'type' => 'widget.user'
            )
        )

    )

);
