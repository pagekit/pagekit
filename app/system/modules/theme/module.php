<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('system.admin', function () use ($app) {

            $app['view']->on('layout', function ($event) use ($app) {
                $event->setTemplate($this->path.'/templates/template.php');
                $event->setParameter('user', $app['user']);
                $event->setParameter('nav', $app['admin.menu']);
                $event->setParameter('subnav', current(array_filter($app['admin.menu']->getChildren(), function ($item) { return $item->getAttribute('active'); })));
                $event->setParameter('subset', 'latin,latin-ext');
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

        });

    },

    'resources' => [

        'system/theme:' => ''

    ]

];
