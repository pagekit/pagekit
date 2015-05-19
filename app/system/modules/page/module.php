<?php

return [

    'name' => 'system/page',

    'main' => 'Pagekit\\Page\\PageModule',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'resources' => [

        'system/page:' => ''

    ],

    'controllers' => [

        '@page: /page' => 'Pagekit\\Page\\Controller\\SiteController',
        '@page/api: /api/page' => 'Pagekit\\Page\\Controller\\PageController'
    ],

    'permissions' => [

        'page: manage pages' => [
            'title' => 'Manage pages'
        ]

    ],

    'templates' => [

        'page.edit' => 'system/page:views/tmpl/edit.php'

    ]
];
