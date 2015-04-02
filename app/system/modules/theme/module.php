<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('system.admin', function () use ($app) {

            $app['view']->on('toolbar', function ($event) {
                $event->setResult(sprintf('<div class="uk-clearfix uk-margin">%s</div>', $event->getResult()));
            });

            $app['view']->on('messages', function ($event) use ($app) {

                $result = '';

                if ($app['message']->peekAll()) {
                    foreach ($app['message']->levels() as $level) {
                        if ($messages = $app['message']->get($level)) {
                            foreach ($messages as $message) {
                                $result .= sprintf('<div class="uk-alert uk-alert-%1$s" data-status="%1$s">%2$s</div>', $level == 'error' ? 'danger' : $level, $message);
                            }
                        }
                    }
                }

                if ($result) {
                    $event->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
                }

            });

            $app['view']->map('layout', $this->path.'/templates/template.php');

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
