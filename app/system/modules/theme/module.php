<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('app.admin', function () use ($app) {

            $app['view']->map('layout', $this->path.'/templates/template.php');
            $app['view']->map('component', $this->path.'/templates/template.php');

            $app['view']->on('layout', function ($event) use ($app) {
                $event->setParameter('user', $app['user']);
                $event->setParameter('nav', $app['admin.menu']);
                $event->setParameter('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));
                $event->setParameter('subset', 'latin,latin-ext');
            });

        });

    },

    'resources' => [

        'system/theme:' => ''

    ]

];
