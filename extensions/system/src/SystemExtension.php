<?php

namespace Pagekit;

use Pagekit\Application as App;
use Pagekit\Content\ContentHelper;
use Pagekit\Extension\Extension;
use Pagekit\Menu\Event\MenuListener;
use Pagekit\Menu\MenuProvider;
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
use Pagekit\User\Entity\Role;
use Pagekit\User\Entity\User as UserEntity;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;
use Pagekit\Widget\Event\WidgetListener;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(App $app)
    {
        if (!(isset($app['config']) ? $app['config']['app.debug'] : true)) {
            $app['events']->addSubscriber(new ExceptionListener('Pagekit\System\Exception\ExceptionController::showAction'));
        }

        $app['events']->addSubscriber(new AccessListener);
        $app['events']->addSubscriber(new AdminMenuListener);
        $app['events']->addSubscriber(new AuthorizationListener);
        $app['events']->addSubscriber(new CanonicalListener);
        $app['events']->addSubscriber(new FrontpageListener);
        $app['events']->addSubscriber(new LocaleListener);
        $app['events']->addSubscriber(new LoginAttemptListener);
        $app['events']->addSubscriber(new MaintenanceListener);
        $app['events']->addSubscriber(new MenuListener);
        $app['events']->addSubscriber(new MigrationListener);
        $app['events']->addSubscriber(new ResponseListener);
        $app['events']->addSubscriber(new SystemListener);
        $app['events']->addSubscriber(new UserListener);
        $app['events']->addSubscriber(new WidgetListener);
        $app['events']->addSubscriber(new ThemeListener);
        $app['events']->addSubscriber(new ThemeWidgetListener);

        parent::boot($app);

        $this->mergeOptions();

        $app['system'] = $this;

        $app['menus'] = function($app) {
            return new MenuProvider;
        };

        $app['user'] = function($app) {

            if (!$user = $app['auth']->getUser()) {
                $user  = new UserEntity;
                $roles = Role::where(['id' => Role::ROLE_ANONYMOUS])->get();
                $user->setRoles($roles);
            }

            return $user;
        };

        $app['permissions'] = function($app) {
            return $app['events']->dispatch('system.permission', new PermissionEvent)->getPermissions();
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

        $app['system.info'] = function() {
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

        if (isset($app['profiler'])) {
            $app->on('system.init', function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['system.info']), 'extensions/system/views/profiler/toolbar/system.php', 'extensions/system/views/profiler/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'extensions/system/views/profiler/toolbar/user.php', null, -20);
            });
        }
    }

    /**
     * @{inheritdoc}
     */
    public function enable()
    {
        if ($version = App::migrator()->create('extensions/system/migrations', App::option()->get('system:version'))->run()) {
            App::option()->set('system:version', $version);
        }

        foreach (['blog', 'page'] as $extension) {
            if ($extension = App::extensions()->get($extension)) {
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
            App::config()->set($key, App::option()->get("system:$key", App::config()->get($key)));
        }
    }
}
