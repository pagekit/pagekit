<?php

return [

    'name' => 'system/widget',

    'main' => 'Pagekit\\Widget\\WidgetModule',

    'include' => 'widgets/widget*.php',

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'routes' => [

        '/widget' => [
            'name' => '@widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetController'
        ],
        '/api/widget' => [
            'name' => '@widget/api',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetApiController'
        ]

    ],

    'resources' => [

        'widget:' => ''

    ],

    'permissions' => [

        'system: manage widgets' => [
            'title' => 'Manage widgets'
        ]

    ],

    'menu' => [

        'site: widgets' => [
            'label' => 'Widgets',
            'parent' => 'site',
            'url' => '@widget',
            'active' => '@widget(/edit)?'
        ]

    ],

    'config' => [

        'widget' => [

            'positions' => [],
            'config' => [],
            'defaults' => []

        ]

    ],

    'events' => [

        'view.layout' => function($event) use ($app) {
            $app['scripts']->register('widgets', 'widget:app/bundle/widgets.js', 'vue');
        },

        'system.widget.postLoad' => function ($event, $widget) use ($app) {
            $widget->position = $app['positions']->find($widget->getId());
        },

        'system.widget.postSave' => function ($event, $widget) use ($app) {
            $app['config']->get('system/widget')->set('widget.positions.'.$widget->position, $app['positions']->assign($widget->position, $widget->getId()));
        }
    ]

];
