<?php

use Pagekit\Site\Event\MaintenanceListener;
use Pagekit\Site\Event\NodesListener;

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'include' => 'widgets/widget-menu.php',

    'routes' => [

        '/' => [
            'name' => '@site',
            'controller' => 'Pagekit\\Site\\Controller\\SiteController'
        ],
        '/api/site/menu' => [
            'name' => '@site/api/menu',
            'controller' => 'Pagekit\\Site\\Controller\\MenuController'
        ],
        '/api/site/node' => [
            'name' => '@site/api/node',
            'controller' => 'Pagekit\\Site\\Controller\\NodeController'
        ]

    ],

    'resources' => [

        'system/site:' => ''

    ],

    'views' => [

        'menu' => 'system/site:views/menu.php'

    ],

    'permissions' => [

        'site: manage site' => [
            'title' => 'Manage site'
        ],
        'site: maintenance access' => [
            'title' => 'Use the site in maintenance mode'
        ]

    ],

    'menu' => [

        'site' => [
            'label' => 'Site',
            'icon' => 'system/site:assets/images/icon-site.svg',
            'url' => '@site',
            'active' => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label' => 'Pages',
            'parent' => 'site',
            'url' => '@site',
            'active' => '@site(/edit)?'
        ],
        'site: settings' => [
            'label' => 'Settings',
            'parent' => 'site',
            'url' => '@site/settings',
            'priority' => 5
        ]

    ],

    'config' => [

        'menus' => [],
        'frontpage' => 0,

        'title' => '',
        'description' => '',

        'maintenance' => [
            'enabled' => false,
            'msg' => ''
        ],

        'icons' => [
            'favicon' => '',
            'appicon' => ''
        ],

        'code' => [
            'header' => '',
            'footer' => ''
        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(
                new MaintenanceListener(),
                new NodesListener()
            );
        },

        'view.head' => [function () use ($app) {
            $app['scripts']->register('panel-link', 'system/site:app/bundle/panel-link.js', 'vue');
            $app['scripts']->register('input-link', 'system/site:app/bundle/input-link.js', 'panel-link');
        }, 50],

        'site.node.postLoad' => function ($event, $entity) {
            $entity->frontpage = $entity->getId() === $this->config('frontpage');
        },

        'site.node.postSave' => function ($event, $entity) use ($app) {
            if ($entity->frontpage || $this->config('frontpage') === $entity->getId()) {
                $app['config']->get('system/site')->set('frontpage', $entity->frontpage ? $entity->getId() : 0);
            }
        },

        'site.node.postDelete' => function ($event, $entity) use ($app) {
            if ($this->config('frontpage') === $entity->getId()) {
                $app['config']->get('system/site')->set('frontpage', 0);
            }
        }

    ]

];
