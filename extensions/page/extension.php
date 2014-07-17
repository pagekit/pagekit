<?php

return [

    'main' => 'Pagekit\\Page\\PageExtension',

    'autoload' => [

        'Pagekit\\Page\\' => 'src'

    ],

    'controllers' => 'src/Controller/*Controller.php',

    'menu' => [

        'page' => [
            'label'    => 'Pages',
            'icon'     => 'extension://page/extension.svg',
            'url'      => '@page/page',
            'active'   => '@page/page*',
            'access'   => 'page: manage pages',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'page: manage pages' => [
            'title' => 'Manage pages'
        ]

    ]

];
