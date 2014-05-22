<?php

namespace Pagekit;

use Pagekit\Comment\Helper\CommentHelper;
use Pagekit\Content\ContentHelper;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Menu\Event\MenuListener;
use Pagekit\Menu\MenuProvider;
use Pagekit\System\DataCollector\SystemDataCollector;
use Pagekit\System\DataCollector\UserDataCollector;
use Pagekit\System\Event\AdminMenuListener;
use Pagekit\System\Event\AliasListener;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\LocaleListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\ResponseListener;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Exception\ExceptionHandler;
use Pagekit\System\Helper\CountryHelper;
use Pagekit\System\Helper\DateHelper;
use Pagekit\System\Helper\LanguageHelper;
use Pagekit\System\Helper\SystemInfoHelper;
use Pagekit\Theme\Event\ThemeListener;
use Pagekit\Theme\Event\WidgetListener as ThemeWidgetListener;
use Pagekit\User\Entity\User as UserEntity;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\UserProvider;
use Pagekit\Widget\Event\WidgetListener;
use Pagekit\Widget\PositionManager;
use Pagekit\Widget\WidgetProvider;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app['exception']->pushHandler(new ExceptionHandler($app['config']['app.debug']));

        $app['events']->addSubscriber(new AccessListener);
        $app['events']->addSubscriber(new AdminMenuListener);
        $app['events']->addSubscriber(new AliasListener);
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

        $app['system'] = $app->protect($this);

        $app['menus'] = function() {
            return new MenuProvider;
        };

        $app['user'] = function($app) {

            if (!$user = $app['auth']->getUser()) {
                $user  = new UserEntity;
                $roles = $app['users']->getRoleRepository()->where(array('id' => RoleInterface::ROLE_ANONYMOUS))->get();
                $user->setRoles($roles);
            }

            return $user;
        };

        $app['users'] = function($app) {
            return new UserProvider($app['caches']['phpfile']);
        };

        $app['widgets'] = function() {
            return new WidgetProvider;
        };

        $app['positions'] = function($app) {
            return new PositionManager($app['widgets']);
        };

        $app['permissions'] = function($app) {

            $event = new PermissionEvent;

            foreach ($app['extensions'] as $extension) {
                if ($permissions = $extension->getConfig('permissions')) {
                    $event->setPermissions($extension->getName(), $permissions);
                }
            }

            return $app['events']->dispatch('system.permission', $event)->getPermissions();
        };

        $app['content'] = function() {
            return new ContentHelper;
        };

        $app['comments'] = function() {
            return new CommentHelper;
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

        $app['dates'] = function($app) {

            $manager = new DateHelper;
            $manager->setTimezone($app['option']->get('system:app.timezone', 'UTC'));
            $manager->setFormats(array(
                DateHelper::NONE      => '',
                DateHelper::FULL      => __('DATE_FULL'),
                DateHelper::LONG      => __('DATE_LONG'),
                DateHelper::MEDIUM    => __('DATE_MEDIUM'),
                DateHelper::SHORT     => __('DATE_SHORT'),
                DateHelper::INTERVAL  => __('DATE_INTERVAL')
            ));

            return $manager;
        };

        if (isset($app['profiler'])) {
            $app->on('system.loaded', function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['system.info']), 'view://system/profiler/toolbar/system.php', 'view://system/profiler/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'view://system/profiler/toolbar/user.php', null, -20);
            });
        }
    }

    /**
     * @{inheritdoc}
     */
    public function enable()
    {
        if ($version = $this('migrator')->run('extension://system/migrations', $this('option')->get('system:version'))) {
            $this('option')->set('system:version', $version);
        }
    }

    /**
     * Clear cache on kernel terminate event.
     */
    public function clearCache(array $options = array())
    {
        $self = $this;

        $this('app')->on('kernel.terminate', function() use ($self, $options) {
            $self->doClearCache($options);
        }, -512);
    }

    /**
     * TODO: clear opcache
     */
    public function doClearCache(array $options = array())
    {
        // clear cache
        if (empty($options) || isset($options['cache'])) {
            $this('cache')->flushAll();
        }

        // clear compiled template files
        if (empty($options) || isset($options['templates'])) {
            $this('file')->delete($this('path.cache').'/templates');
        }

        // clear temp folder
        if (isset($options['temp'])) {
            foreach ($this('file')->find()->in($this('path.temp'))->depth(0)->ignoreDotFiles(true) as $file) {
                $this('file')->delete($file->getPathname());
            }
        }
    }

    protected function mergeOptions()
    {
        $keys = array(
            'app.frontpage',
            'app.locale',
            'app.locale_admin',
            'app.site_description',
            'app.site_title',
            'app.timezone',
            'app.storage',
            'mail.enabled',
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
        );

        foreach ($keys as $key) {
            $this('config')->set($key, $this('option')->get("system:$key", $this('config')->get($key)));
        }

        if (!$this('config')->get('app.storage')) {
            $this('config')->set('app.storage', '/storage');
        }
    }
}
