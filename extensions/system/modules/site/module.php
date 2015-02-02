<?php

use Pagekit\Site\Event\AliasListener;
use Pagekit\Site\Event\MenuEvent;
use Pagekit\Site\Event\RouteListener;
use Pagekit\Site\Event\TypeEvent;

return [

    'name' => 'system/site',

    'main' => function ($app, $config) {

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

        $app->on('site.menus', function($event) use ($app) {
            foreach($app['option']->get('system:site.menus', []) as $menu) {
                $event->register($menu['id'], $menu['label']);
            }
        }, -8);

        $app->on('system.admin_menu', function ($event) use ($config) {
            $event->register($config['menu']);
        });
    },

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Site\\Controller\\MenuController',
            'Pagekit\\Site\\Controller\\NodeController',
            'Pagekit\\Site\\Controller\\SiteController',
            'Pagekit\\Site\\Controller\\TemplateController',
        ]

    ],

    'menu' => [

        'system: site' => [
            'label'    => 'Site',
            'icon'     => 'extensions/page/extension.svg',
            'url'      => '@system/site',
            'active'   => '@system/site*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'system: manage site' => [
            'title' => 'Manage site'
        ]

    ]

];
