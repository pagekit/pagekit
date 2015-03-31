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

    'name' => 'user',

    'main' => function ($app) {

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
            return $app->trigger('user.permission', new PermissionEvent)->getPermissions();
        };

        $app->on('user.permission', function ($event) use ($app) {
            foreach ($app['module'] as $module) {
                if (isset($module->permissions)) {
                    $event->setPermissions($module->name, $module->permissions);
                }
            }
        });

        $app->on('system.widget', function ($event) {
            $event->register(new LoginWidget);
        });

        $app->on('system.dashboard', function ($event, $dashboard) {
            $dashboard->registerType(new UserWidget);
        });

        $app->on('system.settings.edit', function ($event) use ($app) {
            $event->options($this->name, $this->config, ['registration', 'require_verification']);
            $event->view($this->name, 'User', 'app/modules/user/views/admin/settings.php');
        });

    },

    'autoload' => [

        'Pagekit\\User\\' => 'src'

    ],

    'controllers' => [

        '@user: /' => [
            'Pagekit\\User\\Controller\\AuthController',
            'Pagekit\\User\\Controller\\PermissionController',
            'Pagekit\\User\\Controller\\ProfileController',
            'Pagekit\\User\\Controller\\RegistrationController',
            'Pagekit\\User\\Controller\\ResetPasswordController',
            'Pagekit\\User\\Controller\\RoleController',
            'Pagekit\\User\\Controller\\UserController'
        ],

        '@api/user: /api/user' => [
            'Pagekit\\User\\Controller\\RoleApiController',
            'Pagekit\\User\\Controller\\UserApiController'
        ]

    ],

    'menu' => [

        'user' => [
            'label'    => 'Users',
            'icon'     => 'user: assets/images/icon-users.svg',
            'url'      => '@user',
            'active'   => '@user*',
            'access'   => 'user: manage users || user: manage user permissions',
            'priority' => 15
        ],
        'user: sub-user' => [
            'label'    => 'Users',
            'parent'   => 'user',
            'url'      => '@user',
            'active'   => '@user*',
            'access'   => 'user: manage users',
            'priority' => 15
        ],
        'user: user permissions' => [
            'label'    => 'Permissions',
            'parent'   => 'user',
            'url'      => '@user/permission',
            'active'   => '@user/permission*',
            'access'   => 'user: manage user permissions'
        ],
        'user: user roles' => [
            'label'    => 'Roles',
            'parent'   => 'user',
            'url'      => '@user/role',
            'active'   => '@user/role*',
            'access'   => 'user: manage user permissions'
        ]

    ],

    'permissions' => [

        'user: manage users' => [
            'title' => 'Manage users',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'user: manage user permissions' => [
            'title' => 'Manage user permissions',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: access admin area' => [
            'title' => 'Access admin area',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ]

    ],

    'config' => [

        'registration' => 'admin',
        'require_verification' => true,
        'users_per_page' => 20

    ]

];
