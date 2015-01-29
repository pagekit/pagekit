<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Extension\Extension;
use Pagekit\Site\Event\AliasListener;
use Pagekit\Site\Event\MenuEvent;
use Pagekit\Site\Event\RouteListener;
use Pagekit\Site\Event\TypeEvent;

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
            return $app->trigger('site.types', new TypeEvent)->getTypes();
        };

        $app['site.menus'] = function($app) {
            return $app->trigger('site.menus', new MenuEvent)->getMenus();
        };

        $app->on('site.menus', function($event) {
            foreach(App::option('system:site.menus', []) as $menu) {
                $event->register($menu['id'], $menu['label']);
            }
        }, -8);
    }
}
