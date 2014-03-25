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

    'menu' => function($event) {

        $items = array(

            'page' => array(
                'label'  => 'Pages',
                'url'    => '@page/page/index',
                'active' => '/admin/page*',
                'access' => 'page: manage pages'
            )

        );

        $event->addItems($items);
    },

    'permissions' => function($event) {

        $permissions = array(

            'page: manage pages' => array(
                'title' => 'Manage pages'
            )

        );

        $event->setPermissions('page', $permissions);
    }

);
