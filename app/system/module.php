<?php

use Pagekit\Kernel\Event\ExceptionListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'require' => [

        'application',
        'feed',
        'markdown',
        'migration',
        'package',
        'tree',
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
        'system/menu',
        'system/package',
        'system/page',
        'system/settings',
        'system/site',
        'system/theme',
        'system/user',
        'system/widget',
        'system/widget-text'

    ],

    'include' => 'modules/*/module.php',

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

            $app->subscribe(
                new MigrationListener,
                new SystemListener
            );

            if ($app->inConsole()) {
                $app['isAdmin'] = false;
            }

            $app->on('app.request', function ($event, $request) use ($app) {

                if (!$event->isMasterRequest()) {
                    return;
                }

                $app['isAdmin'] = $admin = (bool) preg_match('#^/admin(/?$|/.+)#', $request->getPathInfo());
                $app['intl']->setDefaultLocale($this->config($admin ? 'admin.locale' : 'site.locale'));

            }, 50);

        },

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

        }

    ]

];
