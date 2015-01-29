<?php

use Pagekit\Dashboard\FeedWidget;
use Pagekit\Dashboard\WeatherWidget;

return [

    'name' => 'system/dashboard',

    'main' => function ($app) {

        $app->on('system.dashboard', function ($event) {

            $event->register(new FeedWidget);
            $event->register(new WeatherWidget);

        });

    },

    'autoload' => [

        'Pagekit\\Dashboard\\' => 'src'

    ]

];
