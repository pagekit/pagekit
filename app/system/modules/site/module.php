<?php

use Pagekit\Site\Entity\Node;
use Pagekit\Site\Event\MaintenanceListener;

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

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

        'site:' => ''

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
            'icon' => 'site:assets/images/icon-site.svg',
            'url' => '@site',
            'active' => '@site*',
            'priority' => 105
        ],
        'site: pages' => [
            'label' => 'Pages',
            'parent' => 'site',
            'url' => '@site'
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
        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {

            $app->subscribe(new MaintenanceListener());

        },

        'app.request' => [function () use ($app) {

            foreach (Node::where(['status = ?'], [1])->get() as $node) {

                if (!$type = $this->getType($node->getType())) {
                    continue;
                }

                $type['path']     = $node->getPath();
                $type['defaults'] = array_merge(isset($type['defaults']) ? $type['defaults'] : [], $node->get('defaults', []), ['_node' => $node->getId()]);

                $route = null;
                if (isset($type['alias'])) {
                    $route = $app['routes']->alias($type['path'], $node->getLink($type['alias']), $type['defaults']);
                } elseif (isset($type['controller'])) {
                    $route = $app['routes']->add($type);
                }

                if ($route && $node->getId() == $this->config('frontpage')) {
                    $this->setFrontpage($route->getName());
                }

            }

            if ($this->frontpage) {
                $app['routes']->alias('/', $this->frontpage);
            } else {
                $app['routes']->get('/', function () {
                    return __('No Frontpage assigned.');
                });
            }

        }, 110]

    ]

];
