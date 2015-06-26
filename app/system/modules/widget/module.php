<?php

use Pagekit\Widget\Event\SiteListener;

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

        'boot' => function($event, $app) {
            $app->subscribe(new SiteListener());
        },

        'system.widget.postLoad' => function ($event, $widget) use ($app) {
            $widget->position = $this->getPositions()->find($widget->getId());
        },

        'system.widget.postSave' => function ($event, $widget) use ($app) {

            $config = $app['config']->get('system/widget');

            // if (false !== $config) {
            //     $config->set('widget.config.' . $widget->getId(), $config);
            // }

        }
    ]

];
