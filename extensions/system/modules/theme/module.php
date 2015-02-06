<?php

use Pagekit\Application;

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('system.admin', function () use ($app) {

            $app['view']->setLayout($this->path.'/templates/template.razr');

            $app['sections']->addRenderer('toolbar', function ($name, $value, $options = []) use ($app) {
                return $app['view']->render('extensions/system/modules/theme/views/renderer/toolbar.razr', compact('name', 'value', 'options'));
            });

            $app['sections']->register('toolbar', ['renderer' => 'toolbar']);

            $app->on('kernel.view', function () use ($app) {

                // set title
                $app['sections']->prepend('head', function () use ($app) {

                    $title = $app['view']->get('head.title');

                    if ($site = Application::config('system:settings.site_title')) {
                        $title = "$title &lsaquo; $site";
                    }

                    $app['view']->set('head.title', "$title &#8212; Pagekit");

                });

                // set menus
                $app['view']->set('nav', $app['admin.menu']);
                $app['view']->set('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));

                // set font subset
                $app['view']->set('subset', 'latin,latin-ext');
            });

        });

    }

];
