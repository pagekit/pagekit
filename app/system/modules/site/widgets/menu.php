<?php

return [

    'name' => 'system/menu',

    'label' => 'Menu',

    'defaults' => [
        'start_level' => 1,
        'depth' => 0,
        'mode' => 'all'
    ],

    'render' => function ($widget) use ($app) {
        return $app->view()->menu()->render($widget->get('menu'), 'system/site/widget-menu.php', [
            'start_level' => (int) $widget->get('start_level'),
            'depth' => $widget->get('depth'),
            'mode' => $widget->get('mode'),
            'widget' => $widget
        ]);
    },

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ]

];
