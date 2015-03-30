<?php

use Pagekit\Session\Csrf\Event\CsrfListener;
use Pagekit\Session\Csrf\Provider\SessionCsrfProvider;
use Pagekit\Session\Handler\DatabaseSessionHandler;
use Pagekit\Session\Message;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

return [

    'name' => 'session',

    'main' => function ($app) {

        $app['session'] = function($app) {
            $session = new Session($app['session.storage']);
            $session->registerBag($app['message']);
            return $session;
        };

        $app['message'] = function() {
            return new Message;
        };

        $app['session.storage'] = function($app) {

            switch ($this->config['storage']) {

                case 'database':

                    $handler = new DatabaseSessionHandler($app['db'], $this->config['table']);
                    $storage = new NativeSessionStorage($app['session.options'], $handler);

                    break;

                case 'array':

                    $storage = new MockArraySessionStorage;
                    $app['session.test'] = true;

                    break;

                default:

                    $handler = new NativeFileSessionHandler($this->config['files']);
                    $storage = new NativeSessionStorage($app['session.options'], $handler);

                    break;
            }

            return $storage;
        };

        $app['session.options'] = function() {

            $options = array_diff_key($this->config, array_fill_keys(['name', 'main'], null));

            if (isset($options['cookie'])) {

                foreach ($options['cookie'] as $name => $value) {
                    $options[$name == 'name' ? 'name' : 'cookie_'.$name] = $value;
                }

                unset($options['cookie']);
            }

            if (isset($options['lifetime']) && !isset($options['gc_maxlifetime'])) {
                $options['gc_maxlifetime'] = $options['lifetime'];
            }

            return $options;
        };

        $app['session.test'] = false;

        $app['csrf'] = function($app) {
            return new SessionCsrfProvider($app['session']);
        };

        $app->on('kernel.boot', function() use ($app) {

            if ($app['session.test']) {

                $app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {

                    if (!$event->isMasterRequest() || !isset($app['session'])) {
                        return;
                    }

                    $session = $app['session'];
                    $cookies = $event->getRequest()->cookies;

                    if ($cookies->has($session->getName())) {
                        $session->setId($cookies->get($session->getName()));
                    } else {
                        $session->migrate(false);
                    }

                }, 100);

                $app->on(KernelEvents::RESPONSE, function (FilterResponseEvent $event) {

                    if (!$event->isMasterRequest()) {
                        return;
                    }

                    if ($session = $event->getRequest()->getSession() and $session->isStarted()) {

                        $session->save();

                        $params = session_get_cookie_params();
                        $cookie = new Cookie($session->getName(), $session->getId(), 0 === $params['lifetime'] ? 0 : time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);

                        $event->getResponse()->headers->setCookie($cookie);
                    }

                }, -100);
            }

            $app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {

                if (!$app['session.test'] && !isset($app['session.options']['cookie_path'])) {
                    $app['session.storage']->setOptions(['cookie_path' => $event->getRequest()->getBasePath() ?: '/']);
                }

                $event->getRequest()->setSession($app['session']);

            }, 100);

            $app->subscribe(new CsrfListener($app['csrf']));

        });
    },

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
