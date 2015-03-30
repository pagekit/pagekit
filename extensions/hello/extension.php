<?php

return [

    'name' => 'hello',

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => [

        'Pagekit\\Hello\\' => 'src'

    ],

    'controllers' => [

        '@hello: /hello' => [
            'Pagekit\\Hello\\Controller\\HelloController',
            'Pagekit\\Hello\\Controller\\SiteController'
        ]
    ],

    'config' => [

        'settings.view' => 'hello: views/admin/settings.razr',
        'message' => 'World'

    ],

    'menu' => [

        'hello' => [
            'label'  => 'Hello',
            'icon'   => 'extensions/hello/extension.svg',
            'url'    => '@hello/hello',
            'active' => '@hello/hello*',
            'access' => 'hello: manage hellos'
        ]

    ]

];
