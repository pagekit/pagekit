<?php

return [

    'name' => 'system/meta',

    'events' => [

        'site' => function () use ($app) {

            $app->on('view.meta', function ($event, $meta) use ($app) {

                $config = $app->config('system/site');

                $meta([
                    'og:site_name' => $config->get('title'),
                    'og:title' => $app['node']->title,
                    'og:image' => $config->get('meta.image') ? $app['url']->getStatic($config->get('meta.image'), [], 0) : '',
                    'og:description' => $config->get('meta.description'),
                    'og:url' => $meta->get('canonical'),
                ]);

                if ($app['node']->get('meta')) {
                    $meta($app['node']->get('meta'));
                }

            }, 50);
        },

        'view.system/site/admin/edit' => function ($event, $view) {
            $view->script('node-meta', 'app/system/modules/meta/app/bundle/node-meta.js', 'site-edit');
        }

    ]

];
