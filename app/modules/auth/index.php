<?php

use Pagekit\Auth\Auth;
use Pagekit\Auth\AuthEvents;
use Pagekit\Auth\Encoder\NativePasswordEncoder;
use Pagekit\Auth\Event\AuthenticateEvent;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\RememberMe;
use RandomLib\Factory;

return [

    'name' => 'auth',

    'main' => function ($app) {

        $app['auth'] = function ($app) {
            return new Auth($app['events'], $app['session'], $this->config('auth'));
        };

        $app['auth.password'] = function () {
            return new NativePasswordEncoder;
        };

        $app['auth.random'] = function () {
            return (new Factory)->getMediumStrengthGenerator();
        };

        $app['auth.remember'] = function ($app) {

            $config = [

                'gc_maxlifetime' => $app['session.options']['gc_maxlifetime'],
                'rememberme_lifetime' => $this->config('rememberme.timeout')

            ];

            return new RememberMe($app['session'], $config);
        };

    },

    'events' => [

        'boot' => function ($event, $app) {

            if (!$this->config('rememberme.enabled')) {
                return;
            }

            $app->on('request', function () use ($app) {

                try {

                    if (null !== $app['auth']->getUser()) {
                        return;
                    }

                    $user = $app['auth.remember']->autoLogin($app['auth']->getUserProvider());

                    $app['auth']->setUser($user);
                    $app['events']->trigger(new LoginEvent(AuthEvents::LOGIN, $user), ['auto' => true]);

                } catch (\Exception $e) {}

            }, 49);

            $app->on(AuthEvents::SUCCESS, function (AuthenticateEvent $event) use ($app) {
                $app['auth.remember']->set($app['request'], $event->getUser());
            });

            $app->on(AuthEvents::LOGOUT, function ($event, $auto = false) use ($app) {
                if (!$auto) {
                    $app['auth.remember']->remove();
                }
            });

        }

    ],

    'autoload' => [

        'Pagekit\\Auth\\' => 'src'

    ],

    'config' => [

        'auth' => [

            'timeout' => 900

        ],

        'rememberme' => [

            'enabled' => true,
            'timeout' => 31536000

        ]

    ]

];
