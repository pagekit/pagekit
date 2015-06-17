<?php

use Pagekit\Database\Event\EntityEvent;

return [

    'name' => 'system/page',

    'main' => 'Pagekit\\Page\\PageModule',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'nodes' => [

        'page' => [
            'label' => 'Page',
            'alias' => '@page/id'
        ]

    ],

    'routes' => [

        '/page' => [
            'name' => '@page',
            'controller' => 'Pagekit\\Page\\Controller\\SiteController'
        ],
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

        'view.site:views/edit' => function($event, $view) use ($app) {

            $view->style('codemirror');
            $view->script('page-site', 'system/page:app/bundle/site.js', ['site-edit', 'editor']);

        },

        'site.node.preSave' => function(EntityEvent $event) use ($app) {

            $node = $event->getEntity();
            $data = $app['request']->get('page');

            if ('page' !== $node->getType() or $data === null) {
                return;
            }

            $page = $this->getPage($node);
            $page->save($data);

            $node->set('variables', ['id' => $page->getId()]);

        },

        'site.node.postDelete' => function(EntityEvent $event) use ($app) {

            $node = $event->getEntity();

            if ('page' !== $node->getType()) {
                return;
            }

            $page = $this->getPage($node);

            if ($page->getId()) {
                $page->delete();
            }

        }

    ],

];
