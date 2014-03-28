<?php

$app->on('admin.init', function() use ($app) {

    $app['router']->addController('Pagekit\Alpha\Controller\SettingsController', array('name' => 'alpha'));

});

$app->on('site.init', function() use ($app) {

    $app['positions']->registerRenderer('blank', 'theme://alpha/views/renderer/position.blank.razr.php');
    $app['positions']->registerRenderer('grid', 'theme://alpha/views/renderer/position.grid.php');
    $app['positions']->registerRenderer('navbar', 'theme://alpha/views/renderer/position.navbar.razr.php');
    $app['positions']->registerRenderer('offcanvas', 'theme://alpha/views/renderer/position.offcanvas.razr.php');
    $app['positions']->registerRenderer('panel', 'theme://alpha/views/renderer/position.panel.razr.php');

});

$app->on('view.layout', function($event) use ($app) {

    $event->setParameter('position', $app['positions']);
    $event->setParameter('theme', $app['theme.site']);

});

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

        'Pagekit\\Alpha\\' => 'src'

    ),

    'main' => 'Pagekit\\Alpha\\AlphaTheme',

    'resources' => array(

        'override' => array(
            'extension://system/theme/templates' => 'templates/system'
        )

    ),

    'settings' => '@alpha/settings/index'

);
