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

        'system.widget.postLoad' => function ($event, $widget) use ($app) {
            $widget->position = $this->getPositions()->find($widget->getId());
        },

        'system.widget.postSave' => function ($event, $widget) use ($app) {
            $this->getPositions()->assign($widget->position, $widget->getId());
            $app['config']->get('system/widget')->set('widget.positions', $this->getPositions()->getAssigned());

        }
    ]

];
