<?php

use Pagekit\Page\PageExtension;

return [

    'name' => 'page',

    'main' => function ($app, $config) {

        $extension = new PageExtension();
        $extension->setConfig($config);
        $extension->load($app, $config);

        return $extension;
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
