<?php

namespace Pagekit\User;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Widget\LoginWidget;

class UserModule extends Module
{
    protected $perms = [];

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
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

        $app->on('widget.types', function ($event, $widgets) {
            $widgets->registerType(new LoginWidget());
        });

        $app->on('app.request', function () use ($app) {
            $app['scripts']->register('widget-login', 'system/user:app/bundle/widgets/login.js', '~widgets');
            $app['scripts']->register('widget-user', 'system/user:app/bundle/widgets/user.js', ['~dashboard', 'gravatar']);
        });

    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        if (!$this->perms) {

            foreach (App::module() as $module) {
                if (isset($module->permissions)) {
                    $this->registerPermissions($module->name, $module->permissions);
                }
            }

            App::trigger('user.permission', [$this]);
        }

        return $this->perms;
    }

    /**
     * Register permissions.
     *
     * @param string $extension
     * @param array  $permissions
     */
    public function registerPermissions($extension, array $permissions = [])
    {
        $this->perms[$extension] = $permissions;
    }
}
