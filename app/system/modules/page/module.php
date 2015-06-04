<?php

return [

    'name' => 'system/page',

    'main' => 'Pagekit\\Page\\PageModule',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'nodes' => [

        'page' => [
            'name' => '@page/id',
            'label' => 'Page',
            'alias' => 'true'
        ]

    ],

    'routes' => [

        '/page' => [
            'name' => '@page',
            'controller' => 'Pagekit\\Page\\Controller\\SiteController'
        ],
        '/api/page' => [
            'name' => '@page/api',
            'controller' => 'Pagekit\\Page\\Controller\\PageController'
        ]

    ],

    'resources' => [

        'system/page:' => ''

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
