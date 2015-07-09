<?php

return [

    'name' => 'system/theme',

    'type' => 'theme',

    'events' => [

        'view.layout' => function ($event, $view) use ($app) {

            if (!$app['isAdmin']) {
                return;
            }

            $user = [
                'id' => $app['user']->getId(),
                'name' => $app['user']->getName(),
                'email' => $app['user']->getEmail(),
                'username' => $app['user']->getUsername()
            ];

            $view->data('$pagekit', [
                'editor' => $app['module']['system/editor']->config('editor'),
                'storage' => $app['module']['system/finder']->config('storage'),
                'user' => $user,
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
