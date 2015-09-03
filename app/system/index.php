<?php

use Pagekit\Installer\Package\PackageManager;
use Pagekit\Kernel\Event\ExceptionListener;

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'include' => 'modules/*/index.php',

    'require' => [

        'application',
        'feed',
        'markdown',
        'installer',
        'system/view',
        'system/cache',
        'system/comment',
        'system/content',
        'system/dashboard',
        'system/editor',
        'system/finder',
        'system/info',
        'system/intl',
        'system/mail',
        'system/settings',
        'system/site',
        'system/theme',
        'system/user',
        'system/widget',
        'system/widget-login',
        'system/widget-menu',
        'system/widget-text'

    ],

    'routes' => [

        '/' => [
            'name' => '@system',
            'controller' => 'Pagekit\\System\\Controller\\AdminController'
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

        'api' => 'http://pagekit.com',

        'site' => [

            'theme' => null,
            'locale' => 'en_US'

        ],

        'admin' => [

            'locale' => 'en_US'

        ],

        'extensions' => [],

        'packages' => []

    ],

    'events' => [

        'boot' => function ($event, $app) {

            if (!$app['debug']) {
                $app->subscribe(new ExceptionListener('Pagekit\System\Controller\ExceptionController::showAction'));
            }

        },

        'request' => [

            [function ($event, $request) use ($app) {

                if (!$event->isMasterRequest()) {
                    return;
                }

                $app['isAdmin'] = $admin = (bool)preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());
                $app->module('system/intl')->setLocale($this->config($admin ? 'admin.locale' : 'site.locale'));

            }, 150],

            [function ($event) use ($app) {

                if (!$event->isMasterRequest()) {
                    return;
                }

                $app->trigger($app->isAdmin() ? 'admin' : 'site', [$app]);

            }]

        ],

        'auth.login' => [function ($event) use ($app) {
            if ($event->getUser()->hasAccess('system: software updates') && version_compare($this->config('version'), $app->version()) !== 0) {
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

            $event->setResult(sprintf('<div class="pk-system-messages">%s</div>', $result));
        }

    ]

];
