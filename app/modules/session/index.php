<?php

use Pagekit\Session\Csrf\Event\CsrfListener;
use Pagekit\Session\Csrf\Provider\SessionCsrfProvider;
use Pagekit\Session\Handler\DatabaseSessionHandler;
use Pagekit\Session\MessageBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

return [

    'name' => 'session',

    'main' => function ($app) {

        $app['session'] = function ($app) {
            $session = new Session($app['session.storage']);
            $session->registerBag($app['message']);
            return $session;
        };

        $app['message'] = function () {
            return new MessageBag();
        };

        $app['session.storage'] = function ($app) {

            switch ($this->config['storage']) {

                case 'database':

                    $handler = new DatabaseSessionHandler($app['db'], $this->config['table']);
                    $storage = new NativeSessionStorage($app['session.options'], $handler);

                    break;

                default:

                    $handler = new NativeFileSessionHandler($this->config['files']);
                    $storage = new NativeSessionStorage($app['session.options'], $handler);

                    break;
            }

            return $storage;
        };

        $app['session.options'] = function () {

            $options = $this->config(['cookie', 'lifetime']);

            if (isset($options['cookie'])) {

                foreach ($options['cookie'] as $name => $value) {
                    $options[$name == 'name' ? 'name' : 'cookie_' . $name] = $value;
                }

                unset($options['cookie']);
            }

            if (isset($options['lifetime']) && !isset($options['gc_maxlifetime'])) {
                $options['gc_maxlifetime'] = $options['lifetime'];
            }

            return $options;
        };

        $app['csrf'] = function ($app) {
            return new SessionCsrfProvider($app['session']);
        };

    },

    'events' => [

        'boot' => function ($event, $app) {

            $app->subscribe(new CsrfListener($app['csrf']));

        },

        'request' => [function ($event, $request) use ($app) {

            if (!isset($app['session.options']['cookie_path'])) {
                $app['session.storage']->setOptions(['cookie_path' => $request->getBasePath() ?: '/']);
            }

            $request->setSession($app['session']);

            $app['session']->start();

        }, 100]

    ],

    'autoload' => [

        'Pagekit\\Session\\' => 'src'

    ],

    'config' => [

        'storage'  => null,
        'lifetime' => 900,
        'files'    => null,
        'table'    => 'sessions',
        'cookie'   => [
            'name' => '',
        ]

    ]

];
