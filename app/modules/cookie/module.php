<?php

use Pagekit\Cookie\CookieJar;

return [

    'name' => 'cookie',

    'main' => function ($app) {

        $app['cookie'] = function ($app) {

            $app['cookie.init'] = true;

            if (!$path = $this->config['path']) {
                $path = $app['request']->getBasePath() ?: '/';
            }

            return new CookieJar($app['request'], $path, $this->config['domain']);
        };

    },

    'autoload' => [

        'Pagekit\\Cookie\\' => 'src'

    ],

    'config' => [

        'path'   => null,
        'domain' => null,

    ],

    'events' => [

        'response' => function ($event) use ($app) {
            if (isset($app['cookie.init'])) {
                foreach ($app['cookie']->getQueuedCookies() as $cookie) {
                    $event->getResponse()->headers->setCookie($cookie);
                }
            }
        }

    ]

];
