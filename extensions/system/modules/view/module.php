<?php

use Pagekit\View\ViewListener;

return [

    'name' => 'system/view',

    'main' => function ($app) {
        $app->subscribe(new ViewListener);
    },

    'autoload' => [

        'Pagekit\\View\\' => 'src'

    ]

];
