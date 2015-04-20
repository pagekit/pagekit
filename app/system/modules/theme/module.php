<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('app.admin', function () use ($app) {

            $app['view']->map('layout', $this->path.'/templates/template.php');
            $app['view']->map('component', $this->path.'/templates/template.php');

            $app['view']->on('layout', function ($event, $view) use ($app) {
                $view->setParameter('user', $app['user']);
                $view->setParameter('nav', $app['admin.menu']);
                $view->setParameter('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));
                $view->setParameter('subset', 'latin,latin-ext');
            });

            $app['view']->on('messages', function ($event, $view) use ($app) {

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
                    $view->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
                }

            });

        });

    },

    'resources' => [

        'system/theme:' => ''

    ]

];
