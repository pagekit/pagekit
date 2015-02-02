<?php

use Pagekit\User\Dashboard\UserWidget;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Widget\LoginWidget;

return [

    'name' => 'system/user',

    'main' => function ($app, $config) {

        $app->subscribe(
            new AccessListener,
            new AuthorizationListener,
            new LoginAttemptListener,
            new UserListener
        );

        $app['user'] = function ($app) {

            if (!$user = $app['auth']->getUser()) {
                $user  = new User;
                $roles = Role::where(['id' => Role::ROLE_ANONYMOUS])->get();
                $user->setRoles($roles);
            }

            return $user;
        };

        $app['permissions'] = function ($app) {
            return $app->trigger('system.permission', new PermissionEvent)->getPermissions();
        };

        $app->on('system.admin_menu', function ($event) use ($config) {
            $event->register($config['menu']);
        });

        $app->on('system.permission', function ($event) use ($app) {
            foreach ($app['module']->getConfigs() as $config) {
                if (isset($config['permissions'])) {
                    $event->setPermissions($config['name'], $config['permissions']);
                }
            }
        });

        $app->on('system.widget', function ($event) {
            $event->register(new LoginWidget);
        });

        $app->on('system.dashboard', function ($event) {
            $event->register(new UserWidget);
        });

    },

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
