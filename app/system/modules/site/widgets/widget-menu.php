<?php

use Pagekit\Application as App;

return [

    'name' => 'system/widget-menu',

    'label' => 'Menu',

    'type' => 'widget',

    'render' => function ($widget) use ($app) {

        if (!$menu = $widget->get('menu')) {
            return '';
        }

        $root = App::menu()->getTree($menu, [
            'start_level' => (int) $widget->get('start_level', 1),
            'depth' => $widget->get('depth'),
            'mode' => $widget->get('mode')
        ]);

        if (!$root) {
            return '';
        }

        return $app['view']->render('system/site/widget-menu.php', compact('widget', 'root'));
    },

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ]

];
