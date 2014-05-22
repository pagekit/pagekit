<?php

return array(

    'main' => 'Pagekit\\Page\\PageExtension',

    'autoload' => array(

        'Pagekit\\Page\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'menu' => array(

        'page' => array(
            'label'    => 'Pages',
            'icon'     => 'extension://page/extension.svg',
            'url'      => '@page/page/index',
            'active'   => '/admin/page*',
            'access'   => 'page: manage pages',
            'priority' => 0
        )

    ),

    'permissions' => array(

        'page: manage pages' => array(
            'title' => 'Manage pages'
        )

    )

);
