<?php

use Pagekit\Widget\Model\Widget;

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

        'system/widget:' => '',
        'views:system/widget' => 'views'

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

        'boot' => function ($event, $app) {

            Widget::setProperty('position', function () use ($app) {
                return $app['theme']->findPosition($this->id);
            }, true);

            Widget::setProperty('theme', function () use ($app) {

                $config  = $app['theme']->get("data.widgets.".$this->id, []);
                $default = $app['theme']->get("widget", []);

                return array_replace_recursive($default, $config);
            }, true);
        },

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widgets', 'system/widget:app/bundle/widgets.js', 'vue');
        },

        'model.widget.saved' => function ($event, $widget) use ($app) {
            $app['theme']->assignPosition($widget->position, $widget->id);
            $app['theme']->options['data']['widgets'][$widget->id] = $widget->theme;
            $app['theme']->save();
        },

        'model.role.deleted' => function ($event, $role) {
            Widget::removeRole($role);
        }

    ]

];
