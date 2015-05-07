<?php

use Pagekit\Kernel\Event\ExceptionListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\SystemListener;
use Symfony\Component\Finder\Finder;

return [

    'name' => 'system',

    'main' => function ($app) {

        $app->factory('finder', function () {
            return Finder::create();
        });

        $app['module']['auth']->config['rememberme.key'] = $this->config('key');

        $this->config['storage'] = '/'.trim(($this->config['storage'] ?: 'storage'), '/');
        $app['path.storage'] = rtrim($app['path'].$this->config['storage'], '/');

        $app['db.em']; // -TODO- fix me

        $app['system'] = $this;

        $app['view']->on('messages', function ($event) use ($app) {

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

        });

        foreach ($this->config['extensions'] as $module) {
            try {
                $app['module']->load($module);
            } catch (\RuntimeException $e) {
                $app['log']->warn("Unable to load extension: $module");
            }
        }

        $app['module']->load($theme = $this->config['site.theme']);

        if ($app['theme.site'] = $app['module']->get($theme)) {
            $app->on('app.site', function () use ($app) {
                $app['view']->map('layout', $app['theme.site']->getLayout());
            });
        }

    },

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
