<?php

return array(

    'main' => '%NAMESPACE_ESC%\\%CLASSNAME%',

    'autoload' => array(

        '%NAMESPACE_ESC%\\' => 'src'

    ),

    'resources' => array(

        'export' => array(
            'view' => 'views'
        )

    ),

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => array(

        'system' => '%NAME%/admin/settings.razr'

    )

);
