<?php

use Pagekit\Kernel\Event\ExceptionListener;

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'include' => 'modules/*/module.php',

    'require' => [

        'application',
        'feed',
        'markdown',
        'migration',
        'package',
        'system/view',
        'system/cache',
        'system/comment',
        'system/console',
        'system/content',
        'system/dashboard',
        'system/editor',
        'system/finder',
        'system/info',
        'system/mail',
        'system/package',
        'system/page',
        'system/settings',
        'system/site',
        'system/theme',
        'system/user',
        'system/widget',
        'system/widget-login',
        'system/widget-menu',
        'system/widget-text'

    ],

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'routes' => [

        '/' => [
            'name' => '@system',
            'controller' => 'Pagekit\\System\\Controller\\AdminController'
        ],
        '/system/intl' => [
            'name' => '@system/intl',
            'controller' => 'Pagekit\\System\\Controller\\IntlController'
        ],
        '/system/migration' => [
            'name' => '@system/migration',
            'controller' => 'Pagekit\\System\\Controller\\MigrationController'
        ]

    ],

    'resources' => [

        'system:' => ''

    ],

    'config' => [

        'key' => '',

        'site' => [
            'locale' => 'en_US',
            'theme' => 'alpha'
        ],

        'admin' => [
            'locale' => 'en_US'
        ],

        'timezone' => 'UTC',

        'extensions' => []

    ],

    'events' => [

        'boot' => function ($event, $app) {

            if (!$app['debug']) {
                $app->subscribe(new ExceptionListener('Pagekit\System\Controller\ExceptionController::showAction'));
            }

            if ($app->inConsole()) {
                $app['isAdmin'] = false;
            }

        },

        'request' => [

            [function ($event, $request) use ($app) {

                if (!$event->isMasterRequest()) {
                    return;
                }

                $app['isAdmin'] = $admin = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());

                $app->extend('translator', function ($translator) use ($app) {

                    $locale = $this->config($app['isAdmin'] ? 'admin.locale' : 'site.locale');
                    $app['intl']->setDefaultLocale($locale);
                    $translator->setLocale($locale);
                    $this->loadLocale($locale, $translator);

                    return $translator;
                });

            }, 50],

            [function ($event, $request) use ($app) {

                if (!$event->isMasterRequest()) {
                    return;
                }

                $app->trigger($app['isAdmin'] ? 'admin' : 'site', [$request]);

            }]

        ],

        'auth.login' => [function ($event) use ($app) {

            if ($event->getUser()->hasAccess('system: software updates') && $app['migrator']->create('system:migrations', $this->config('version'))->get()) {
                $event->setResponse($app['response']->redirect('@system/migration'));

            }

        }, 8],

        'view.messages' => function ($event) use ($app) {

            $result = '';

            if ($app['message']->peekAll()) {
                foreach ($app['message']->levels() as $level) {
                    if ($messages = $app['message']->get($level)) {
                        foreach ($messages as $message) {
                            $result .= sprintf('<div class="uk-alert uk-alert-%1$s" data-status="%1$s">%2$s</div>', $level == 'error' ? 'danger' : $level, $message);
                        }
                    }
                }
            }

            if ($result) {
                $event->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
            }

        },

        'view.layout' => function($event, $view) use ($app) {

            if ($event->getTemplate() !== 'layout') {
                return;
            }

            if ($theme = $app['isAdmin'] ? $app['module']['system/theme'] : $app['theme'] ? : null) {
                $event->setParameter('theme', $theme);
                $event->setTemplate($theme->getLayout());
            }

        }

    ]

];
