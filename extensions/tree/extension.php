<?php

return [

    'main'        => 'Pagekit\\Tree\\TreeExtension',

    'autoload'    => [

        'Pagekit\\Tree\\' => 'src'

    ],

    'controllers' => [

        '/tree' => 'Pagekit\\Tree\\Controller\\NodeController'

    ],

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
            'url'      => '@tree/node',
            'active'   => '@tree/node*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'tree: manage nodes' => [
            'title' => 'Manage nodes'
        ]
    ]

];
