<?php

return [

    'name' => 'system/page',

    'main' => 'Pagekit\\Page\\PageModule',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'routes' => [

        '@page/id' => [
            'label' => 'Page',
            'type' => 'page',
            'alias' => 'true'
        ],
        '@page' => [
            'path' => '/page',
            'controller' => 'Pagekit\\Page\\Controller\\SiteController'
        ],
        '@page/api' => [
            'path' => '/api/page',
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
