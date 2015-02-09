<?php

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'controllers' => [

        '@system: /system' => [
            'Pagekit\\Site\\Controller\\MenuController',
            'Pagekit\\Site\\Controller\\NodeController',
            'Pagekit\\Site\\Controller\\SiteController',
            'Pagekit\\Site\\Controller\\TemplateController',
        ]

    ],

    'menu' => [

        'system: site' => [
            'label'    => 'Site',
            'icon'     => 'extensions/page/extension.svg',
            'url'      => '@system/site',
            'active'   => '@system/site*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'system: manage site' => [
            'title' => 'Manage site'
        ]

    ]

];
