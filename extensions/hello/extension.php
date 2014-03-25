<?php

return array(

    'main' => 'Pagekit\\Hello\\HelloExtension',

    'autoload' => array(

        'Pagekit\\Hello\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view'  => 'views',
            'asset' => 'assets'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => '@hello/hello/settings',

    'menu' => function($event) {

        $items = array(

            'hello' => array(
                'label'  => __('Hello'),
                'url'    => '@hello/hello/index',
                'active' => '/admin/hello*',
                'access' => 'hello: manage hellos'
            )

        );

        $event->addItems($items);
    }

);
