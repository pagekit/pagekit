<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('system.admin', function () use ($app) {

            $app['view']->setLayout($this->path.'/templates/template.php');

            $app['view']->on('toolbar', function ($event) {
                $event->setResult(sprintf('<div class="uk-clearfix uk-margin">%s</div>', $event->getResult()));
            });

            $app->on('kernel.view', function () use ($app) {

                // set user
                $app['view']->addGlobal('user', $app['user']);

                // set menus
                $app['view']->addGlobal('nav', $app['admin.menu']);
                $app['view']->addGlobal('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));

                // set font subset
                $app['view']->addGlobal('subset', 'latin,latin-ext');

            });

        });

    }

];
