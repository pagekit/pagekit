<?php

$app->on('site.init', function() use ($app) {

    $app['positions']->registerRenderer('grid', 'theme://alpha/views/renderer/position.grid.php');
    $app['positions']->registerRenderer('panel', 'theme://alpha/views/renderer/position.panel.razr.php');
    $app['positions']->registerRenderer('blank', 'theme://alpha/views/renderer/position.blank.razr.php');
    $app['positions']->registerRenderer('navbar', 'theme://alpha/views/renderer/position.navbar.razr.php');
    $app['positions']->registerRenderer('offcanvas', 'theme://alpha/views/renderer/position.offcanvas.razr.php');

});

$app->on('boot', function() use ($app) {
    $app['router']->addController('Pagekit\DefaultTheme\Controller\SettingsController', array('name' => 'alpha'));
});

// return the theme's config array
return array(

    'positions' => array(

        'logo'      => 'Logo',
        'navbar'    => 'Navbar',
        'top'       => 'Top',
        'sidebar'   => 'Sidebar',
        'footer'    => 'Footer',
        'offcanvas' => 'Offcanvas'

    ),

    'autoload' => array(

        'Pagekit\\DefaultTheme\\' => 'src'

    ),

    'resources' => array(

        'override' => array(
            'extension://system/theme/templates' => 'templates/system'
        )

    ),

    'settings' => '@alpha/settings/index',

);