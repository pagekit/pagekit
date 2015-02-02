<?php

use Pagekit\Page\PageExtension;

return [

    'name' => 'page',

    'main' => function ($app, $config) {

        return new PageExtension($app, $config);

    },

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'controllers' => [

        '@page: /page' => 'Pagekit\\Page\\Controller\\SiteController'

    ],

    'permissions' => [

        'page: manage pages' => [
            'title' => 'Manage pages'
        ]

    ]

];
