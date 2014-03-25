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

    'menu' => function($event) {

        $items = array(

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

        );

        $event->addItems($items);
    },

    'permissions' => function($event) {

        $permissions = array(

            'system: manage menus' => array(
                'title' => __('Manage menus')
            ),
            'system: manage widgets' => array(
                'title' => __('Manage widgets')
            ),
            'system: manage themes' => array(
                'title' => __('Manage themes')
            ),
            'system: manage extensions' => array(
                'title' => __('Manage extensions')
            ),
            'system: manage url aliases' => array(
                'title' => __('Manage url aliases')
            ),
            'system: manage users' => array(
                'title' => __('Manage users'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: manage user permissions' => array(
                'title' => __('Manage user permissions'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: access admin area' => array(
                'title' => __('Access admin area'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: access settings' => array(
                'title' => __('Access system settings'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: software updates' => array(
                'title' => __('Apply system updates'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: manage storage' => array(
                'title' => __('Manage storage'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: maintenance access' => array(
                'title' => __('Use the site in maintenance mode')
            )

        );

        $event->setPermissions('system', $permissions);
    },

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
            $scripts->register('uikit-notify', 'vendor://assets/uikit/addons/notify/notify.js', array(), array('requirejs' => true));

        }

    ),

    'locale' => function($event) {

        $date = array(
            'short'       => __('DATE_SHORT'),
            'medium'      => __('DATE_MEDIUM'),
            'long'        => __('DATE_LONG'),
            'full'        => __('DATE_FULL'),
            'shortdays'   => array(__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')),
            'longdays'    => array(__('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')),
            'shortmonths' => array(__('Jan'), __('Feb'), __('Mar'), __('Apr'), __('May'), __('Jun'), __('Jul'), __('Aug'), __('Sep'), __('Oct'), __('Nov'), __('Dec')),
            'longmonths'  => array(__('January'), __('February'), __('March'), __('April'), __('May'), __('June'), __('July'), __('August'), __('September'), __('October'), __('November'), __('December'))
        );

        $event->addMessages($date, 'date');
    }

);
