<?php

namespace Pagekit\User;

use Pagekit\Application as App;
use Pagekit\Extension\Extension;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;

class UserModule extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        parent::load($app, $config);

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
    }
}
