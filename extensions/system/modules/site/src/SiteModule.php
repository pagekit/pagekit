<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Extension\Extension;
use Pagekit\Site\Event\AliasListener;
use Pagekit\Site\Event\RouteListener;
use Pagekit\Site\Event\TypeEvent;
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;

class SiteModule extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        parent::load($app, $config);

        $app->subscribe(
            new AliasListener,
            new RouteListener
        );

        $app['site.types'] = function($app) {
            return $app->trigger('site.types', new TypeEvent);
        };
    }
}
