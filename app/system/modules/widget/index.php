<?php

return [

    'name' => 'system/widget',

    'main' => 'Pagekit\\Widget\\WidgetModule',

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'routes' => [

        '/site/widget' => [
            'name' => '@site/widget',
            'controller' => 'Pagekit\\Widget\\Controller\\WidgetController'
        ],
        '/api/site/widget' => [
            'name' => '@site/api/widget',
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
            'url' => '@site/widget',
            'access' => 'system: manage widgets',
            'active' => '@site/widget(/edit)?'
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

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widgets', 'widget:app/bundle/widgets.js', 'vue');
        },

        'model.widget.init' => function ($event, $widget) use ($app) {
            $widget->theme = $app['theme']->getWidget($widget->getId());
            $widget->position = $app['theme']->findPosition($widget->getId());
        },

        'model.widget.saved' => function ($event, $widget) use ($app) {
            $app['theme']->configWidget($widget->theme, $widget->getId());
            $app['theme']->assignPosition($widget->position, $widget->getId());
        }
    ]

];
