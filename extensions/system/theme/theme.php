<?php

$app->on('system.admin', function() use ($app) {

    $app->on('kernel.view', function() use ($app) {

        // set title
        $app['view.sections']->prepend('head', function() use ($app) {

            $title = $app['view']->get('head.title');

            if ($site = $app['config']->get('app.site_title')) {
                $title = "$title &lsaquo; $site";
            }

            $app['view']->set('head.title', "$title &#8212; Pagekit");

        });

        // set menus
        $app['view']->set('nav', $app['admin.menu']);
        $app['view']->set('subnav', current(array_filter($app['admin.menu']->getChildren(), function($item) { return $item->getAttribute('active'); })));

        // set font subset
        $app['view']->set('subset', 'latin,latin-ext');
    });

    $app['view.sections']->addRenderer('toolbar', function($name, $value, $options = []) use ($app) {
        return $app['view']->render('extension://system/theme/views/renderer/toolbar.razr', compact('name', 'value', 'options'));
    });

    $app['view.sections']->register('toolbar', ['renderer' => 'toolbar']);

});