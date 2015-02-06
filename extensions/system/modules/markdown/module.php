<?php

use Pagekit\Markdown\Markdown;

return [

    'name' => 'system/markdown',

    'main' => function ($app) {

        $app['markdown'] = function() {
            return new Markdown;
        };

    },

    'autoload' => [

        'Pagekit\\Markdown\\' => 'src'

    ]

];
