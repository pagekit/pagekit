<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('system.admin', function () use ($app) {

            $app['view']->setLayout($this->path.'/templates/template.php');

            $app['sections']->addRenderer('toolbar', function ($name, $value, $options = []) use ($app) {
                return $app['tmpl']->render('app/modules/theme/views/renderer/toolbar.php', compact('name', 'value', 'options'));
            });

            $app['sections']->register('toolbar', ['renderer' => 'toolbar']);

            $app->on('kernel.view', function () use ($app) {

                // set title
                $app['sections']->prepend('head', function () use ($app) {

                    $title = $app['view']->meta()->get('title');

                    if ($site = $app['system']->config('site.title')) {
                        $title = "$title &lsaquo; $site";
                    }

                    $app['view']->meta(['title' => "$title &#8212; Pagekit"]);

                });

                // set user
                $app['tmpl']->addGlobal('user', $app['user']);

                // set menus
                $app['tmpl']->addGlobal('nav', $app['admin.menu']);
                $app['tmpl']->addGlobal('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));

                // set font subset
                $app['tmpl']->addGlobal('subset', 'latin,latin-ext');

            });

        });

    }

];
