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
            'label'  => 'Pages',
            'url'    => '@page/page/index',
            'active' => '/admin/page*',
            'access' => 'page: manage pages'
        )

    ),

    'permissions' => array(

        'page: manage pages' => array(
            'title' => 'Manage pages'
        )

    )

);
