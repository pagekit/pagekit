<?php

use Pagekit\Cookie\CookieJar;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

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

        $app->on('kernel.response', function (FilterResponseEvent $event) use ($app) {
            if (isset($app['cookie.init'])) {
                foreach ($app['cookie']->getQueuedCookies() as $cookie) {
                    $event->getResponse()->headers->setCookie($cookie);
                }
            }
        });

    },

    'autoload' => [

        'Pagekit\\Cookie\\' => 'src'

    ],

    'config' => [

        'path'   => null,
        'domain' => null,

    ]

];
