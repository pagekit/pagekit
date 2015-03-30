<?php

return [

    'name' => 'system/marketplace',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['scripts']->register('marketplace', 'app/modules/marketplace/app/marketplace.js', 'vue-system');
        });

    },

    'templates' => [

        'marketplace.main' => 'app/modules/marketplace/views/main.php'

    ]

];
