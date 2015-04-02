<?php

return [

    'main' => '%NAMESPACE_ESC%\\%CLASSNAME%',

    'autoload' => [

        '%NAMESPACE_ESC%\\' => 'src'

    ],

    'resources' => [
        // your resources here...
    ],

    'controllers' => 'src/Controller/*Controller.php',

    'settings' => [

        'system' => 'extensions/%NAME%/views/admin/settings.razr'

    ]

];
