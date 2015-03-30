<?php

use Pagekit\Feed\FeedFactory;

return [

    'name' => 'feed',

    'main' => function ($app) {

        $app['feed'] = function () {
            return new FeedFactory;
        };

    },

    'autoload' => [

        'Pagekit\\Feed\\' => 'src'

    ]

];
