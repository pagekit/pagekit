<?php

use Pagekit\Application as App;

return [

    'name' => 'system/widget-menu',

    'label' => 'Menu',

    'type' => 'widget',

    'render' => function ($widget) use ($app) {
        return App::view()->menu($widget->get('menu'), [
            'start_level' => (int) $widget->get('start_level', 1),
            'depth' => $widget->get('depth'),
            'mode' => $widget->get('mode')
        ]);
    },

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ]

];
