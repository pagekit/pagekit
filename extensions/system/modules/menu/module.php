<?php

use Pagekit\Menu\MenuProvider;
use Pagekit\Menu\Widget\MenuWidget;

return [

    'name' => 'system/menu',

    'main' => function ($app, $config) {

        $app['menus'] = function() {
            return new MenuProvider;
        };

        $app->on('system.loaded', function() use ($app) {
            $app['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
            $app['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
            $app['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
            $app['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');
        });

        $app->on('system.widget', function($event) {
            $event->register(new MenuWidget);
        });
    },

    'autoload' => [

        'Pagekit\\Menu\\' => 'src'

    ],

];
