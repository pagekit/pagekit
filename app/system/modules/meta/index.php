<?php

use Pagekit\Meta\Collection;

return [

    'name' => 'system/meta',

    'main' => function ($app) {

        $app['meta'] = function () use ($app) {
            return new Collection('og');
        };

    },

    'autoload' => [

        'Pagekit\\Meta\\' => 'src'

    ],

    'events' => [

        'site' => function () use ($app) {

            $app['meta']['site_name'] = $app->config('system/site')->get('title');
            $app['meta']['title'] = $app['node']->title;

            $app['meta']['image'] = $app->config('system/site')->get('meta.logo') ? $app['url']->getStatic($app->config('system/site')->get('meta.logo'), [], 0) : '';
            $app['meta']['description'] = $app->config('system/site')->get('meta.description');

            $app['meta']->merge($app['node']->get('meta'));

            $app->on('view.meta', function () use ($app) {

                $app['meta']['url'] = $app['view']->meta()->get('canonical');

                $app['view']->meta($app['meta']->getValues());

            });
        },

        'view.system/site/admin/edit' => function ($event, $view) {
            $view->script('node-meta', 'app/system/modules/meta/app/bundle/node-meta.js', 'site-edit');
        }

    ]

];
