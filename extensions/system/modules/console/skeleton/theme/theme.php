<?php

// config array
return [

    'main' => '%NAMESPACE_ESC%\\%CLASSNAME%',

    'autoload' => [
        '%NAMESPACE_ESC%\\' => 'src'
    ],


    // positions are sections in your theme where widgets can be published
    'positions' => [
        'logo'       => 'Logo',
        // ...
    ],

    // renderers define the markup that is rendered in widget positions
    'renderer' => [
        'blank'     => 'theme://%NAME%/views/renderer/position.blank.razr',
        // ...
    ],

    'resources' => [
        // your resources here...
    ],

    'settings' => [
        'system'  => 'theme://%NAME%/views/admin/settings.razr'
    ]

];