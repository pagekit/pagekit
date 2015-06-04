<?php

return [

    'name' => 'system/cache',

    'main' => 'Pagekit\\Cache\\CacheModule',

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'routes' => [

        '/system' => [
            'name' => '@system',
            'controller' => 'Pagekit\\Cache\\Controller\\CacheController'
        ]

    ],

    'config' => [

        'caches' => [],
        'nocache' => false

    ]

];
