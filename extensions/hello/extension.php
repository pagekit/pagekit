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

    'settings' => array(

        'system'  => 'hello/admin/settings.razr.php'

    ),

    'menu' => array(

        'hello' => array(
            'label'  => 'Hello',
            'url'    => '@hello/hello/index',
            'active' => '@hello/hello*',
            'access' => 'hello: manage hellos'
        )

    )

);
