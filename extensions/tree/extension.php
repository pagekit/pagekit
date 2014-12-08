<?php

return [

    'main'        => 'Pagekit\\Tree\\TreeExtension',

    'autoload'    => [

        'Pagekit\\Tree\\' => 'src'

    ],

    'controllers' => 'src/Controller/*Controller.php',

    'resources'   => [

        'export' => [
            'view'  => 'views',
            'asset' => 'assets'
        ]

    ],

    'menu'        => [

        'system: tree' => [
            'label'    => 'Tree',
            'icon'     => 'extension://page/extension.svg',
            'url'      => '@tree/pages',
            'active'   => '@tree/pages*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'tree: manage pages' => [
            'title' => 'Manage pages'
        ]
    ]

];
