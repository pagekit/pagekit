<?php

$app->on('admin.init', function() use ($app) {

    // set title
    $app['view']->addAction('head', function() use ($app) {

        $title = $app['view']->get('meta.title');

        if ($site = $app['config']->get('app.site_title')) {
            $title = "$title &lsaquo; $site";
        }

        $app['view']->set('meta.title', "$title &#8212; Pagekit");

    }, 8);

    // set menus
    $app->on('kernel.view', function() use ($app) {
        $app['view']->set('nav', $root = $app['menus']->getTree('admin', array('access' => true)));
        $app['view']->set('subnav', current(array_filter($root->getChildren(), function($item) { return $item->getAttribute('active'); })));
    });

});

return array();