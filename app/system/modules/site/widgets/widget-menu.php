<?php

return [

    'name' => 'system/widget-menu',

    'label' => 'Menu',

    'type' => 'widget',

    'views' => [
        'menu' => 'system/site:views/widget-menu.php'
    ],

    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('widget-menu', 'system/site:app/bundle/widget-menu.js', '~widgets');
        }

    ],

    'render' => function ($widget) use ($app) {

        if (!$menu = $widget->get('menu')) {
            return '';
        }

        $root = $app['menu']->render($menu, [
            'start_level' => (int) $widget->get('start_level', 1),
            'depth' => $widget->get('depth'),
            'mode' => $widget->get('mode')
        ]);

        return $app['view']->render('menu', compact('widget', 'root'));
    }

];
