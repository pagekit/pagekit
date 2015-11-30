<?php

use Pagekit\Site\Event\MaintenanceListener;
use Pagekit\Site\Event\NodesListener;
use Pagekit\Site\Event\PageListener;
use Pagekit\Site\Model\Node;

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

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

    'widgets' => [

        'widgets/menu.php',
        'widgets/text.php'

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
            'url' => '@site/page',
            'access' => 'site: manage site || system: manage widgets || system: manage storage || system: manage settings',
            'active' => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label' => 'Pages',
            'parent' => 'site',
            'url' => '@site/page',
            'access' => 'site: manage site',
            'active' => '@site/page(/edit)?'
        ],
        'site: settings' => [
            'label' => 'Settings',
            'parent' => 'site',
            'url' => '@site/settings',
            'access' => 'system: manage settings',
            'priority' => 30
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
            'favicon' => false,
            'appicon' => false
        ],

        'code' => [
            'header' => '',
            'footer' => ''
        ],

        'view' => [

            'logo' => ''

        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {

            $app->subscribe(
                new MaintenanceListener(),
                new NodesListener(),
                new PageListener()
            );

            Node::defineProperty('theme', function () use ($app) {

                $config = $app['theme']->config('_nodes.' . $this->id, []);
                $default = $app['theme']->get('node', []);

                return array_replace_recursive($default, $config);
            }, true);

        },

        'site' => function ($event, $app) {

            $app->on('view.head', function ($event) use ($app) {
                $event->addResult($this->config('code.header'));
            }, -10);

            $app->on('view.footer', function ($event) use ($app) {
                $event->addResult($this->config('code.footer'));
            }, -10);

            $app->on('view.layout', function ($event) use ($app) {
                $event
                    ->addParameters($this->config('view'))
                    ->addParameters($app['theme']->config)
                    ->addParameters($app['node']->theme);
            }, 50);

        },

        'view.meta' => function ($event, $meta) use ($app) {

            $meta->add('link:favicon', [
                'href' => $app['url']->getStatic($this->config('icons.favicon') ?: 'system/theme:favicon.ico'),
                'rel' => 'shortcut icon',
                'type' => 'image/x-icon'
            ]);

            $meta->add('link:appicon', [
                'href' => $app['url']->getStatic($this->config('icons.appicon') ?: 'system/theme:apple_touch_icon.png'),
                'rel' => 'apple-touch-icon-precomposed'
            ]);

        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('panel-link', 'system/site:app/bundle/panel-link.js', 'vue');
            $scripts->register('input-link', 'system/site:app/bundle/input-link.js', 'panel-link');
            $scripts->register('input-tree', 'system/site:app/bundle/input-tree.js', 'vue');
            $scripts->register('link-page', 'system/site:app/bundle/link-page.js', '~panel-link');
            $scripts->register('node-page', 'system/site:app/bundle/node-page.js', ['~site-edit', 'editor']);
        },

        'model.node.saved' => function ($event, $node) use ($app) {
            $app->config($app['theme']->name)->set('_nodes.' . $node->id, $node->theme);
        }

    ]

];
