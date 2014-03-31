<?php

$app->on('admin.init', function() use ($app) {

    $app['router']->addController('Pagekit\Alpha\Controller\SettingsController', array('name' => 'alpha'));

});

$app->on('system.position.renderer', function($event) use ($app) {

    $event->register('blank', 'theme://alpha/views/renderer/position.blank.razr.php');
    $event->register('grid', 'theme://alpha/views/renderer/position.grid.php');
    $event->register('navbar', 'theme://alpha/views/renderer/position.navbar.razr.php');
    $event->register('offcanvas', 'theme://alpha/views/renderer/position.offcanvas.razr.php');
    $event->register('panel', 'theme://alpha/views/renderer/position.panel.razr.php');

});

$app->on('view.layout', function($event) use ($app) {

    $event->setParameter('position', $app['positions']);
    $event->setParameter('theme', $app['theme.site']);

});

$app->on('system.widget.settings', function($event) {

    $event->addSettings('Alpha', 'theme://alpha/views/admin/widgets/edit.razr.php');

});

return array(

    'positions' => array(

        'footer'        => 'Footer',
        'logo'          => 'Logo',
        'navbar'        => 'Navbar',
        'offcanvas'     => 'Offcanvas',
        'sidebar-left'  => 'Sidebar Left',
        'sidebar-right' => 'Sidebar Right',
        'top'           => 'Top'

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

    'settings' => '@alpha/settings/index',

);
