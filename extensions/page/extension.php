<?php

return [

    'name' => 'page',

    'main' => 'Pagekit\\Page\\PageExtension',

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

    ],

    'templates' => [

        'page.edit' => 'page: views/tmpl/edit.php'

    ]
];
