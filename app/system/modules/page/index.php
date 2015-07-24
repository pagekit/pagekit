<?php

return [

    'name' => 'system/page',

    'main' => 'Pagekit\\Page\\PageModule',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'nodes' => [

        'page' => [
            'name' => '@page',
            'label' => 'Page',
            'controller' => 'Pagekit\\Page\\Controller\\SiteController::indexAction'
        ]

    ],

    'routes' => [

        '/api/page' => [
            'name' => '@page/api',
            'controller' => 'Pagekit\\Page\\Controller\\PageController'
        ]

    ],

    'resources' => [

        'system/page:' => ''

    ],

    'permissions' => [

        'page: manage pages' => [
            'title' => 'Manage pages'
        ]

    ],

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('page-link', 'system/page:app/bundle/link.js', '~panel-link');
            $scripts->register('page-site', 'system/page:app/bundle/site.js', ['~site-edit', 'editor']);
        },

        'model.node.saving' => function($event, $node) use ($app) {

            if ('page' !== $node->getType() or null === $data = $app['request']->get('page')) {
                return;
            }

            $page = $this->getPage($node);
            $page->save($data);

            $node->set('defaults', ['id' => $page->getId()]);
            $node->setLink('@page/'.$page->getId());

        },

        'model.node.deleted' => function($event, $node) use ($app) {

            if ('page' !== $node->getType()) {
                return;
            }

            $page = $this->getPage($node);

            if ($page->getId()) {
                $page->delete();
            }

        },

        'route.configure' => function ($event, $route, $routes) use ($app) {
            if ($route->getName() === '@page') {
                $routes->remove('@page');
                $route->setName('@page/'.$route->getDefault('id'));
                $routes->add($route->getName(), $route);
            }
        }

    ],

];
