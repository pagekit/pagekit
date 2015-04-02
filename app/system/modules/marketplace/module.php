<?php

return [

    'name' => 'system/marketplace',

    'main' => function ($app) {

        $app->on('system.init', function() use ($app) {
            $app['scripts']->register('marketplace', 'app/system/modules/marketplace/app/marketplace.js', 'vue-system');
        });

    },

    'templates' => [

        'marketplace.main' => 'app/system/modules/marketplace/views/main.php'

    ]

];
