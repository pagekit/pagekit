<?php

return [

    'main' => 'Pagekit\\Page\\PageExtension',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'controllers' => [

        '/page' => 'Pagekit\\Page\\Controller\\SiteController'

    ],

    'permissions' => [

        'page: manage pages' => [
            'title' => 'Manage pages'
        ]

    ]

];
