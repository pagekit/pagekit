<?php

use Pagekit\Ogp\Manager;

return [

    'name' => 'system/ogp',

    'main' => function ($app) {

        $app['ogp'] = function () {
            return new Manager();
        };

    },

    'autoload' => [

        'Pagekit\\Ogp\\' => 'src'

    ],

    'events' => [

        'site' => function () use ($app) {
            $app->on('view.meta', function () use ($app) {
                $app['view']->meta()->add('og:site_name', $app->config('system/site')->get('title'));
                $app['view']->meta()->add('og:title', $app['view']->meta()->get('title'));
                $app['view']->meta()->add('og:descriptin', '');
                $app['view']->meta()->add('og:url', $app['view']->meta()->get('canonical'));

                if ($app->config('system/site')->get('view.logo')) {
                    $app['view']->meta()->add('og:image', $app['url']->getStatic($app->config('system/site')->get('view.logo'), [], 0));
                }

            });
        }
    ]

];
