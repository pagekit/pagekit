<?php

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

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('page-link', 'system/page:app/bundle/link.js', '~v-linkpicker');
            $scripts->register('page-site', 'system/page:app/bundle/site.js', ['~site-edit', 'editor']);
        },

        // TODO workaround, until the editor is made lazy
        'view.system/site:views/edit' => function($event, $view) use ($app) {
            $view->style('codemirror');
        },

        'site.node.preSave' => function($event) use ($app) {

            $node = $event->getEntity();
            $data = $app['request']->get('page');

            if ('page' !== $node->getType() or $data === null) {
                return;
            }

            $page = $this->getPage($node);
            $page->save($data);

            $node->set('variables', ['id' => $page->getId()]);

        },

        'site.node.postDelete' => function($event) use ($app) {

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
