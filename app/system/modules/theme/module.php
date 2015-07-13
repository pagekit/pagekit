<?php

return [

    'name' => 'system/theme',

    'type' => 'theme',

    'events' => [

        'view.meta' => [function($event, $meta) use ($app) {
            $meta([
                'link:shortcut icon' => [
                    'href' => $app['url']->getStatic('system/theme:favicon.ico'),
                    'type' => 'image/x-icon'
                ],
                'link:apple-touch-icon-precomposed' => [
                    'href' => $app['url']->getStatic('system/theme:apple_touch_icon.png')
                ]
            ]);
        }, 10],

        'view.layout' => function ($event, $view) use ($app) {

            if (!$app['isAdmin']) {
                return;
            }

            $view->data('$pagekit', [
                'editor' => $app['module']['system/editor']->config('editor'),
                'storage' => $app['module']['system/finder']->config('storage'),
                'user' => [
                    'id' => $app['user']->getId(),
                    'name' => $app['user']->getName(),
                    'email' => $app['user']->getEmail(),
                    'username' => $app['user']->getUsername()
                ],
                'menu' => array_values($app['system']->getMenu()->getItems())
            ]);

            $event->setParameter('subset', 'latin,latin-ext');

        }

    ],

    'resources' => [

        'system/theme:' => ''

    ],

    'views' => [

        'error' => 'system/theme:templates/error.php'

    ],

];
