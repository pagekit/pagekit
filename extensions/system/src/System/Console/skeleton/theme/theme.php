<?php

// config array
return array(

    'main' => '%NAMESPACE_ESC%\\%CLASSNAME%',

    'autoload' => array(
        '%NAMESPACE_ESC%\\' => 'src'
    ),


    // positions are sections in your theme where widgets can be published
    'positions' => array(
        'logo'       => 'Logo',
        // ...
    ),

    // renderers define the markup that is rendered in widget positions
    'renderer' => array(
        'blank'     => 'theme://%NAME%/views/renderer/position.blank.razr',
        // ...
    ),

    'resources' => array (
        // your resources here...
    ),

    'settings' => array(
        'system'  => 'theme://%NAME%/views/admin/settings.razr'
    )

);