<?php

namespace Pagekit;

use Pagekit\Application as App;
use Pagekit\Content\ContentHelper;
use Pagekit\Extension\Extension;
use Pagekit\Menu\Event\MenuListener;
use Pagekit\Menu\MenuProvider;
use Pagekit\Site\Event\AliasListener;
use Pagekit\Site\Event\TypeEvent;
use Pagekit\Site\Event\RouteListener;
use Pagekit\System\DataCollector\SystemDataCollector;
use Pagekit\System\DataCollector\UserDataCollector;
use Pagekit\System\Event\AdminMenuListener;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\LocaleListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\ResponseListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Helper\CountryHelper;
use Pagekit\System\Helper\DateHelper;
use Pagekit\System\Helper\LanguageHelper;
use Pagekit\System\Helper\OAuthHelper;
use Pagekit\System\Helper\SystemInfoHelper;
use Pagekit\System\Mail\ImpersonatePlugin;
use Pagekit\Theme\Event\ThemeListener;
use Pagekit\Theme\Event\WidgetListener as ThemeWidgetListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\Widget\Event\WidgetListener;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(App $app, array $config)
    {
        if (!(isset($app['config']) ? $app['config']['app.debug'] : true)) {
            $app->subscribe(new ExceptionListener('Pagekit\System\Exception\ExceptionController::showAction'));
        }

        $app->subscribe(
            new AdminMenuListener,
            new AliasListener,
            new CanonicalListener,
            new FrontpageListener,
            new LocaleListener,
            new MaintenanceListener,
            new MenuListener,
            new MigrationListener,
            new ResponseListener,
            new RouteListener,
            new SystemListener,
            new ThemeListener,
            new ThemeWidgetListener,
            new WidgetListener
        );

        parent::load($app, $config);

        $this->mergeOptions();

        $app['db.em']; // -TODO- fix me

        $app['menus'] = function() {
            return new MenuProvider;
        };

        $app['site.types'] = function($app) {
            return $app->trigger('site.types', new TypeEvent);
        };

        $app['content'] = function() {
            return new ContentHelper;
        };

        $app['languages'] = function() {
            return new LanguageHelper;
        };

        $app['countries'] = function() {
            return new CountryHelper;
        };

        $app['systemInfo'] = function() {
            return new SystemInfoHelper;
        };

        $app['oauth'] = function() {
            return new OAuthHelper;
        };

        $app['dates'] = function($app) {

            $manager = new DateHelper;
            $manager->setTimezone($app['option']->get('system:app.timezone', 'UTC'));
            $manager->setFormats([
                DateHelper::NONE      => '',
                DateHelper::FULL      => __('DATE_FULL'),
                DateHelper::LONG      => __('DATE_LONG'),
                DateHelper::MEDIUM    => __('DATE_MEDIUM'),
                DateHelper::SHORT     => __('DATE_SHORT'),
                DateHelper::INTERVAL  => __('DATE_INTERVAL')
            ]);

            return $manager;
        };

        $app->extend('mailer', function($mailer, $app) {

            $address = $app['config']->get('mail.from.address');
            $name    = $app['config']->get('mail.from.name');

            $mailer->registerPlugin(new ImpersonatePlugin($address, $name));

            return $mailer;
        });

        $app->factory('finder', function() {
            return Finder::create();
        });

        if (isset($app['profiler'])) {
            $app->on('system.init', function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['systemInfo']), 'extensions/system/views/profiler/toolbar/system.php', 'extensions/system/views/profiler/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'extensions/system/views/profiler/toolbar/user.php', null, -20);
            });
        }
    }

    /**
     * @{inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('extensions/system/migrations', App::option('system:version'))->run()) {
            App::option()->set('system:version', $version);
        }

        foreach (['blog', 'page'] as $extension) {
            if ($extension = App::extension($extension)) {
                $extension->enable();
            }
        }
    }

    /**
     * Clear cache on kernel terminate event.
     */
    public function clearCache(array $options = [])
    {
        App::on('kernel.terminate', function() use ($options) {
            $this->doClearCache($options);
        }, -512);
    }

    /**
     * TODO: clear opcache
     */
    public function doClearCache(array $options = [])
    {
        // clear cache
        if (empty($options) || isset($options['cache'])) {
            App::cache()->flushAll();

            foreach (glob(App::get('path.cache') . '/*.cache') as $file) {
                @unlink($file);
            }
        }

        // clear compiled template files
        if (empty($options) || isset($options['templates'])) {
            App::file()->delete(App::get('path.cache').'/templates');
        }

        // clear temp folder
        if (isset($options['temp'])) {
            foreach (App::finder()->in(App::get('path.temp'))->depth(0)->ignoreDotFiles(true) as $file) {
                App::file()->delete($file->getPathname());
            }
        }
    }

    protected function mergeOptions()
    {
        $keys = [
            'app.frontpage',
            'app.locale',
            'app.locale_admin',
            'app.site_description',
            'app.site_title',
            'app.timezone',
            'mail.driver',
            'mail.encryption',
            'mail.from.address',
            'mail.from.name',
            'mail.host',
            'mail.password',
            'mail.port',
            'mail.username',
            'maintenance.enabled',
            'maintenance.msg',
            'theme.site'
        ];

        foreach ($keys as $key) {
            App::config()->set($key, App::option("system:$key", App::config($key)));
        }
    }
}
