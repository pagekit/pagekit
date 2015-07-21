<?php

use Pagekit\Site\Event\MaintenanceListener;
use Pagekit\Site\Event\NodesListener;

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'include' => 'widgets/widget-*.php',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

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

        'system/site:' => '',
        'views:system/site' => 'views'

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

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('panel-link', 'system/site:app/bundle/panel-link.js', 'vue');
            $scripts->register('input-link', 'system/site:app/bundle/input-link.js', 'panel-link');
        },

        'site' => function () use ($app) {

            $app->on('view.meta', function ($event, $meta) use ($app) {

                if ($app['isAdmin']) {
                    return;
                }

                if ($icon = $this->config('icons.favicon')) {
                    $meta->add('link:favicon', [
                        'href' => $app['url']->getStatic($icon),
                        'rel' => 'shortcut icon',
                        'type' => 'image/x-icon'
                    ]);
                }

                if ($icon = $this->config('icons.appicon')) {
                    $meta->add('link:appicon', [
                        'href' => $app['url']->getStatic($icon),
                        'rel' => 'apple-touch-icon-precomposed'
                    ]);
                }

            });

            $app->on('view.head', function ($event, $view) use ($app) {
                $event->addResult($this->config('code.header'));
            }, -10);

            $app->on('view.footer', function ($event, $view) use ($app) {
                $event->addResult($this->config('code.footer'));
            }, -10);

        },

        'site.node.postLoad' => function ($event, $node) use ($app) {
            if ('link' === $node->getType() && $node->get('redirect')) {
                $node->setLink($node->getPath());
            }
        }

    ]

];
