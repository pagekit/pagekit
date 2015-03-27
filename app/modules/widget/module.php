<?php

use Pagekit\Widget\Event\WidgetListener;
use Pagekit\Widget\TextWidget;

return [

    'name' => 'system/widget',

    'main' => function ($app) {

        $app->subscribe(
            new WidgetListener
        );

        $app->on('system.widget', function ($event) {
            $event->register(new TextWidget);
        });

        $app->on('system.positions', function($event) use ($app) {

            foreach ($app['module'] as $module) {

                if (!isset($module->positions) || !is_array($module->positions)) {
                    continue;
                }

                foreach ($module->positions as $id => $position) {
                    list($name, $description) = array_merge((array) $position, ['']);
                    $event->register($id, $name, $description);
                }
            }

        });

    },

    'autoload' => [

        'Pagekit\\Widget\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Widget\\Controller\\WidgetsController'
        ]

    ],

    'menu' => [

        'system: widgets' => [
            'label'    => 'Widgets',
            'icon'     => 'app/modules/system/assets/images/icon-widgets.svg',
            'url'      => '@system/widgets',
            'active'   => '@system/widgets*',
            'access'   => 'system: manage widgets',
            'priority' => 5
        ]

    ],

    'permissions' => [

        'system: manage widgets' => [
            'title' => 'Manage widgets'
        ]

    ]

];
