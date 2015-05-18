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

        '@page: /page' => 'Pagekit\\Page\\Controller\\SiteController'

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
