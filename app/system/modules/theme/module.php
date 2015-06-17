<?php

return [

    'name' => 'system/theme',

    'events' => [

        'app.admin' => function () use ($app) {

            $app['view']->map('layout', $this->path.'/templates/template.php');
            $app['view']->map('component', $this->path.'/templates/template.php');

        },

        'view.layout' => function ($event, $view) use ($app) {

            $user = [
                'id' => $app['user']->getId(),
                'name' => $app['user']->getName(),
                'email' => $app['user']->getEmail(),
                'username' => $app['user']->getUsername()
            ];

            $view->data('$pagekit', [
                'editor' => $app['module']['system/editor']->config('editor'),
                'storage' => $app['system']->config('storage'),
                'user' => $user,
                'menu' => array_values($app['system']->getMenu()->getItems())
            ]);

            $event->setParameter('subset', 'latin,latin-ext');
        }

    ],

    'resources' => [

        'system/theme:' => ''

    ]

];
