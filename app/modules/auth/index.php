<?php

use Pagekit\Auth\Auth;
use Pagekit\Auth\AuthEvents;
use Pagekit\Auth\Encoder\NativePasswordEncoder;
use Pagekit\Auth\Event\AuthenticateEvent;
use Pagekit\Auth\Event\LoginEvent;
use Pagekit\Auth\Event\LogoutEvent;
use Pagekit\Auth\RememberMe;
use RandomLib\Factory;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
            return new RememberMe($this->config('rememberme.key'), $this->config('rememberme.cookie.name') ?: 'remember_'.md5($app['request']->getUriForPath('')), $app['cookie']);
        };

    },

    'events' => [

        'boot' => function ($event, $app) {

            $app->on('auth.login', function (LoginEvent $event) use ($app) {
                $event->setResponse(new RedirectResponse($app['request']->get(Auth::REDIRECT_PARAM)));
            }, -32);

            $app->on('auth.logout', function (LogoutEvent $event) use ($app) {
                $event->setResponse(new RedirectResponse($app['request']->get(Auth::REDIRECT_PARAM)));
            }, -32);

            if (!$this->config('rememberme.enabled') || !$this->config('rememberme.key')) {
                return;
            }

            $app->on('request', function () use ($app) {

                try {

                    if (null !== $app['auth']->getUser()) {
                        return;
                    }

                    $user = $app['auth.remember']->autoLogin($app['auth']->getUserProvider());

                    $app['auth']->setUser($user);
                    $app['events']->trigger(AuthEvents::LOGIN, new LoginEvent($user));

                } catch (\Exception $e) {
                }

            }, 20);

            $app->on(AuthEvents::SUCCESS, function (AuthenticateEvent $event) use ($app) {
                $app['auth.remember']->set($app['request'], $event->getUser());
            });

            $app->on(AuthEvents::FAILURE, function () use ($app) {
                $app['auth.remember']->remove();
            });

            $app->on('auth.logout', function () use ($app) {
                $app['auth.remember']->remove();
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

            'enabled' => true

        ]

    ]

];
