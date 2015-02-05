<?php

use Pagekit\Page\PageExtension;

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

    ]

];
