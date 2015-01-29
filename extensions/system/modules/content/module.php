<?php

use Pagekit\Content\ContentHelper;

return [

    'name' => 'system/content',

    'main' => function ($app) {

        $app['content'] = function() {
            return new ContentHelper;
        };

    },

    'autoload' => [

        'Pagekit\\Content\\' => 'src'

    ]

];
