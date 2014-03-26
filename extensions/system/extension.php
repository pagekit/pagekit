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

    'events' => array(

        'admin.menu' => function($event) {

            $event->addItems(array(

                'system: settings' => array(
                    'label'    => __('Settings'),
                    'url'      => '@system/system/index',
                    'active'   => '(/admin/system|/admin/system/(settings|themes|extensions|storage|alias|update|info|marketplace|dashboard/(settings|add|edit))*)',
                    'priority' => 20
                ),
                'system: widgets' => array(
                    'label'    => __('Widgets'),
                    'url'      => '@system/widgets/index',
                    'active'   => '/admin/system/widgets*',
                    'access'   => 'system: manage widgets',
                    'priority' => 5
                ),
                'system: menu' => array(
                    'label'    => __('Menus'),
                    'url'      => '@system/menu/index',
                    'active'   => '/admin/system/menu*',
                    'access'   => 'system: manage menus',
                    'priority' => 10
                ),
                'system: user' => array(
                    'label'    => __('Users'),
                    'url'      => '@system/user/index',
                    'active'   => '/admin/system/user*',
                    'access'   => 'system: manage users || system: manage user permissions',
                    'priority' => 15
                ),
                'system: user list' => array(
                    'label'    => __('Users'),
                    'parent'   => 'system: user',
                    'url'      => '@system/user/index',
                    'active'   => '/admin/system/user(/edit*)?',
                    'access'   => 'system: manage users'
                ),
                'system: user permissions' => array(
                    'label'    => __('Permissions'),
                    'parent'   => 'system: user',
                    'url'      => '@system/permission/index',
                    'active'   => '/admin/system/user/permission*',
                    'access'   => 'system: manage user permissions'
                ),
                'system: user roles' => array(
                    'label'    => __('Roles'),
                    'parent'   => 'system: user',
                    'url'      => '@system/role/index',
                    'active'   => '/admin/system/user/role*',
                    'access'   => 'system: manage user permissions'
                ),
                'system: user access' => array(
                    'label'    => __('Access'),
                    'parent'   => 'system: user',
                    'url'      => '@system/accesslevel/index',
                    'active'   => '/admin/system/user/access*',
                    'access'   => 'system: manage user permissions'
                )

            ));
        }

    ),

    'dashboard' => array(

        'default' => array(
            '1' => array(
                'type' => 'widget.user'
            )
        )

    ),

    'view' => array(

        'scripts' => function($scripts) {

            $scripts->register('jquery', 'vendor://assets/jquery/jquery.js', array(), array('requirejs' => true));
            $scripts->register('requirejs', 'vendor://assets/requirejs/require.min.js', array('requirejs-config'));
            $scripts->register('requirejs-config', 'asset://system/js/require.js');
            $scripts->register('uikit', 'vendor://assets/uikit/js/uikit.min.js', array(), array('requirejs' => true));
            $scripts->register('uikit-notify', 'vendor://assets/uikit/js/addons/notify.js', array(), array('requirejs' => true));

        }

    )

);
