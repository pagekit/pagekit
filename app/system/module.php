<?php

use Pagekit\Kernel\Event\ExceptionListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;

return [

    'name' => 'system',

    'main' => 'Pagekit\\System\\SystemModule',

    'boot' => function ($app) {

        if (!$app['debug']) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Controller\ExceptionController::showAction'));
        }

        $app->subscribe(
            new MaintenanceListener,
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

        $app->on('app.request', function () use ($app) {
            foreach ($app['module'] as $module) {

                if (!isset($module->resources)) {
                    continue;
                }

                foreach ($module->resources as $prefix => $path) {
                    $app['locator']->add($prefix, "$module->path/$path");
                }
            }
        }, 2);

    },

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
        'system/oauth',
        'system/package',
        'system/settings',
        'system/site',
        'system/theme',
        'system/user',
        'system/widget'

    ],

    'include' => 'modules/*/module.php',

    'autoload' => [

        'Pagekit\\System\\' => 'src'

    ],

    'resources' => [

        'system:' => ''

    ],

    'controllers' => [

        '@system: /' => [
            'Pagekit\\System\\Controller\\AdminController'
        ],

        '@system: /system' => [
            'Pagekit\\System\\Controller\\IntlController',
            'Pagekit\\System\\Controller\\MigrationController',
            'Pagekit\\System\\Controller\\UpdateController',
            'Pagekit\\System\\Controller\\SystemController'
        ]

    ],

    'permissions' => [

        'system: access settings' => [
            'title' => 'Access system settings',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: software updates' => [
            'title' => 'Apply system updates',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: manage storage' => [
            'title' => 'Manage storage',
            'description' => 'Warning: Give to trusted roles only; this permission has security implications.'
        ],
        'system: manage storage read only' => [
            'title' => 'Manage storage (Read only)'
        ],
        'system: maintenance access' => [
            'title' => 'Use the site in maintenance mode'
        ]

    ],

    'config' => [

        'key' => '',

        'api' => [
            'key' => '',
            'url' => 'http://pagekit.com/api',
        ],

        'site' => [
            'title' => '',
            'description' => '',
            'locale' => 'en_US',
            'theme' => 'alpha'
        ],

        'admin' => [
            'locale' => 'en_US'
        ],

        'maintenance' => [
            'enabled' => false,
            'msg' => ''
        ],

        'timezone' => 'UTC',

        'storage' => '/storage',

        'extensions' => [],

        'release_channel' => 'stable'

    ]

];
