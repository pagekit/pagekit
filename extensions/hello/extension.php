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

    'menu' => array(

        'hello' => array(
            'label'  => 'Hello',
            'url'    => '@hello/hello/index',
            'active' => '/admin/hello*',
            'access' => 'hello: manage hellos'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => '@hello/hello/settings'

);
