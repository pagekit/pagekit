<?php

use Pagekit\Ogp\Manager;

return [

    'name' => 'system/ogp',

    'main' => function ($app) {

        $app['ogp'] = function () use ($app) {
            return new Manager('og');
        };

    },

    'autoload' => [

        'Pagekit\\Ogp\\' => 'src'

    ],

    'events' => [

        'site' => function () use ($app) {

            $app['ogp']['site_name'] = $app->config('system/site')->get('title');

            if ($app->config('system/site')->get('view.logo')) {
                $app['ogp']['image'] = $app['url']->getStatic($app->config('system/site')->get('view.logo'), [], 0);
            }

            $app['ogp']->merge($app['node']->get('meta'));

            $app->on('view.meta', function () use ($app) {

                if (!isset($app['ogp']['title'])) {
                    $app['ogp']['title'] = $app['view']->meta()->get('title');
                }

                $app['ogp']['canonical'] = $app['view']->meta()->get('canonical');

                $app['view']->meta($app['ogp']->getValues());

            });
        },

        'view.system/site/admin/edit' => function ($event, $view) {
            $view->script('node-meta', 'app/system/modules/ogp/app/bundle/node-meta.js', 'site-edit');
        }

    ]

];
