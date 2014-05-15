<?php

$app->on('system.admin', function() use ($app) {

    // set title
    $app['view']->addAction('head', function() use ($app) {

        $title = $app['view']->get('head.title');

        if ($site = $app['config']->get('app.site_title')) {
            $title = "$title &lsaquo; $site";
        }

        $app['view']->set('head.title', "$title &#8212; Pagekit");

    }, 8);

    // set menus
    $app->on('kernel.view', function() use ($app) {
        $app['view']->set('nav', $app['admin.menu']);
        $app['view']->set('subnav', current(array_filter($app['admin.menu']->getChildren(), function($item) { return $item->getAttribute('active'); })));
    });

});
