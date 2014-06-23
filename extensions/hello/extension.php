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

        'system'  => 'hello/admin/settings.razr'

    ),

    'menu' => array(

        'hello' => array(
            'label'  => 'Hello',
            'icon'   => 'extension://hello/extension.svg',
            'url'    => '@hello/hello',
            'active' => '@hello/hello*',
            'access' => 'hello: manage hellos'
        )

    )

);
