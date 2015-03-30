<?php

use Pagekit\Comment\CommentPlugin;

return [

    'name' => 'comment',

    'main' => function ($app) {

        $app->subscribe(new CommentPlugin);

    },

    'autoload' => [

        'Pagekit\\Comment\\' => 'src'

    ]

];
