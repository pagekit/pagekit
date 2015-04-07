<?php

return [

    'name' => 'cache',

    'main' => 'Pagekit\\Cache\\CacheModule',

    'autoload' => [

        'Pagekit\\Cache\\' => 'src'

    ],

    'config' => [

        'caches'  => [],
        'nocache' => false

    ]

];
