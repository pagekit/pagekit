<?php

use Pagekit\Menu\MenuProvider;
use Pagekit\Menu\Widget\MenuWidget;

return [

    'name' => 'system/menu',

    'main' => function ($app) {

        $app['menus'] = function() {
            return new MenuProvider;
        };

    },

    'autoload' => [

        'Pagekit\\Menu\\' => 'src'

    ],

    'resources' => [

        'system/menu:' => ''

    ],

    'views' => [

        'menu' => 'system/menu:views/menu.php'

    ],

    'events' => [

        'request' => function() use ($app) {

            $app['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
            $app['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
            $app['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
            $app['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        },

        'widget.types' => function($event, $widgets) {

            $widgets->registerType(new MenuWidget);

        },

        'view.site:views/index' => function ($event, $view) use ($app) {

            $app['scripts']->register('widget-menu', 'system/menu:app/bundle/widgets/menu.js', '~widgets');

        }

    ]

];
