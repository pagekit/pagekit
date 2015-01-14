<?php

return [

    'main'        => 'Pagekit\\Tree\\TreeExtension',

    'autoload'    => [

        'Pagekit\\Tree\\' => 'src'

    ],

    'controllers' => [

        '/tree' => [
            'Pagekit\\Tree\\Controller\\NodeController',
            'Pagekit\\Tree\\Controller\\TemplateController'
        ]

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
            'icon'     => 'extensions/page/extension.svg',
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
