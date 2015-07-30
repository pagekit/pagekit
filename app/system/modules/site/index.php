<?php

use Pagekit\Site\Event\MaintenanceListener;
use Pagekit\Site\Event\NodesListener;
use Pagekit\Site\Event\PageListener;

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'include' => 'widgets/widget-*.php',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'nodes' => [

        'page' => [
            'name' => '@page',
            'label' => 'Page',
            'controller' => 'Pagekit\\Site\\Controller\\PageController::indexAction'
        ]

    ],

    'routes' => [

        '/' => [
            'name' => '@site',
            'controller' => 'Pagekit\\Site\\Controller\\NodeController'
        ],
        '/api/site/menu' => [
            'name' => '@site/api/menu',
            'controller' => 'Pagekit\\Site\\Controller\\MenuApiController'
        ],
        '/api/site/node' => [
            'name' => '@site/api/node',
            'controller' => 'Pagekit\\Site\\Controller\\NodeApiController'
        ],
        '/api/site/page' => [
            'name' => '@site/api/page',
            'controller' => 'Pagekit\\Site\\Controller\\PageApiController'
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
        ],
        'site: manage settings' => [
            'title' => 'Manage settings',
            'description' => 'View and change settings'
        ]

    ],

    'menu' => [

        'site' => [
            'label' => 'Site',
            'icon' => 'system/site:assets/images/icon-site.svg',
            'url' => '@site/page',
            'access' => 'site: manage site',
            'active' => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label' => 'Pages',
            'parent' => 'site',
            'url' => '@site/page',
            'active' => '@site/page(/edit)?'
        ],
        'site: settings' => [
            'label' => 'Settings',
            'parent' => 'site',
            'url' => '@site/settings',
            'priority' => 20
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
        ],

        'logo' => ''

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(
                new MaintenanceListener(),
                new NodesListener(),
                new PageListener()
            );
        },

        'site' => function () use ($app) {

            $app->on('view.head', function ($event, $view) use ($app) {
                $event->addResult($this->config('code.header'));
            }, -10);

            $app->on('view.footer', function ($event, $view) use ($app) {
                $event->addResult($this->config('code.footer'));
            }, -10);

        },

        'view.meta' => function ($event, $meta) use ($app) {

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

        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('panel-link', 'system/site:app/bundle/panel-link.js', 'vue');
            $scripts->register('input-link', 'system/site:app/bundle/input-link.js', 'panel-link');
            $scripts->register('page-link', 'system/site:app/bundle/page-link.js', '~panel-link');
            $scripts->register('page-site', 'system/site:app/bundle/page-site.js', ['~site-edit', 'editor']);
        }

    ]

];
