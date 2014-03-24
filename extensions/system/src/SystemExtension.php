<?php

namespace Pagekit;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Content\MarkdownEditor;
use Pagekit\Content\Plugin\LinkPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Extension\Extension;
use Pagekit\Framework\Application;
use Pagekit\Menu\Event\MenuListener;
use Pagekit\Menu\MenuProvider;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\UserWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\DataCollector\SystemDataCollector;
use Pagekit\System\DataCollector\UserDataCollector;
use Pagekit\System\Event\AdminMenuListener;
use Pagekit\System\Event\AliasListener;
use Pagekit\System\Event\CanonicalListener;
use Pagekit\System\Event\DashboardInitEvent;
use Pagekit\System\Event\FrontpageListener;
use Pagekit\System\Event\LocaleEvent;
use Pagekit\System\Event\LocaleListener;
use Pagekit\System\Event\MaintenanceListener;
use Pagekit\System\Event\MigrationListener;
use Pagekit\System\Event\RegisterTmplEvent;
use Pagekit\System\Event\SystemListener;
use Pagekit\System\Exception\ExceptionHandler;
use Pagekit\System\Helper\CountryHelper;
use Pagekit\System\Helper\DateHelper;
use Pagekit\System\Helper\FinderHelper;
use Pagekit\System\Helper\LanguageHelper;
use Pagekit\System\Helper\SystemInfoHelper;
use Pagekit\System\Link\LinkManager;
use Pagekit\System\Templating\DateHelper as TemplatingDateHelper;
use Pagekit\System\Templating\EditorHelper;
use Pagekit\System\Templating\FinderHelper as TemplatingFinderHelper;
use Pagekit\System\Widget\LoginWidget;
use Pagekit\System\Widget\MenuWidget;
use Pagekit\System\Widget\TextWidget;
use Pagekit\Theme\Event\ThemeListener;
use Pagekit\User\Auth\UserProvider as AuthUserProvider;
use Pagekit\User\Entity\User as UserEntity;
use Pagekit\User\Event\AccessListener;
use Pagekit\User\Event\AuthorizationListener;
use Pagekit\User\Event\LoginAttemptListener;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Model\RoleInterface;
use Pagekit\User\UserProvider;
use Pagekit\Widget\Event\WidgetListener;
use Pagekit\Widget\Model\TypeManager;
use Pagekit\Widget\PositionManager;
use Pagekit\Widget\WidgetProvider;
use Razr\SimpleFilter;
use Razr\SimpleFunction;

class SystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $config = $this->getConfig();

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
        $app['events']->addSubscriber(new SystemListener);
        $app['events']->addSubscriber(new ThemeListener);
        $app['events']->addSubscriber(new UserListener);
        $app['events']->addSubscriber(new WidgetListener);

        parent::boot($app);

        $this->mergeOptions();

        $app['system'] = $app->protect($this);

        $app['menus'] = function($app) {
            return new MenuProvider($app);
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

        $app['widgets'] = function($app) {
            return new WidgetProvider($app['db.em']->getRepository('Pagekit\Widget\Entity\Widget'), new TypeManager);
        };

        $app['positions'] = function($app) {
            return new PositionManager($app['view'], $app['widgets']);
        };

        $app['permissions'] = function($app) {

            $permissions = array();

            foreach ($app['extensions'] as $extension) {
                if ($config = $extension->getConfig('permissions')) {
                    $permissions[$extension->getName()] = $config;
                }
            }

            return $permissions;
        };

        $app['languages'] = function() {
            return new LanguageHelper;
        };

        $app['countries'] = function() {
            return new CountryHelper;
        };

        $app['finder'] = function() {
            return new FinderHelper;
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

        $app['links'] = function() {
            return new LinkManager;
        };

        if (isset($app['profiler'])) {
            $app->before(function() use ($app) {
                $app['profiler']->add(new SystemDataCollector($app['system.info']), 'view://system/profiler/toolbar/system.php', 'view://system/profiler/panel/system.php', 50);
                $app['profiler']->add(new UserDataCollector($app['auth']), 'view://system/profiler/toolbar/user.php', null, -20);
            });
        }

        $app->on('init', function() use ($app, $config) {

            $config['view']['scripts']($app['view.scripts']);

            $helper = new TemplatingDateHelper($app['dates']);
            $app['tmpl.php']->addHelpers(array($helper));
            $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('date', array($helper, 'format')));

            $helper = new EditorHelper($app);
            $app['tmpl.php']->addHelpers(array($helper));
            $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('editor', array($helper, 'render')));

            $helper = new TemplatingFinderHelper($app);
            $app['tmpl.php']->addHelpers(array($helper));
            $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('finder', array($helper, 'render')));

            $app['auth']->setUserProvider(new AuthUserProvider($app['auth.encoder.native']));
            $app['auth']->refresh($app['option']->get(UserListener::REFRESH_TOKEN));

            $app['widgets']->registerType(new LoginWidget);
            $app['widgets']->registerType(new MenuWidget);
            $app['widgets']->registerType(new TextWidget);

            $app['links']->register('Pagekit\System\Link\Frontpage');
            $app['links']->register('Pagekit\System\Link\Url');

            $app['events']->addSubscriber(new LinkPlugin);
            $app['events']->addSubscriber(new VideoPlugin);
            $app['events']->addSubscriber(new SimplePlugin);
            $app['events']->addSubscriber(new MarkdownEditor);

            $app['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
            $app['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
            $app['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
            $app['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

            $app['view']->addAction('head', function(ActionEvent $event) use ($app) {

                foreach ($scripts = $app['view.scripts'] as $script) {

                    if ($script->getName() != 'requirejs') {
                        continue;
                    }

                    foreach ($scripts as $script) {

                        $dependencies = (array) $script['dependencies'];

                        if (isset($script['requirejs'])) {
                            $script['dependencies'] = array_merge($dependencies, array('requirejs'));
                        } elseif (in_array('requirejs', $dependencies)) {
                            $scripts->dequeue($name = $script->getName());
                            $scripts->queue($name);
                        }
                    }

                    break;
                }

            }, 5);

            $app['view']->addAction('messages', function(ActionEvent $event) use ($app) {
                $event->append($app['view']->render('system/messages/messages.razr.php'));
            });
        });

        $app->on('system.locale', $config['locale']);

        $app->on('system.dashboard.init', function(DashboardInitEvent $event) {
            $event->registerType(new FeedWidget);
            $event->registerType(new UserWidget);
            $event->registerType(new WeatherWidget);
        });

        $app->on('view.register.tmpl', function(RegisterTmplEvent $event) {
            $event->register('feed.error', 'extension://system/assets/tmpl/feed.error.razr.php');
            $event->register('feed.list', 'extension://system/assets/tmpl/feed.list.razr.php');
            $event->register('finder.main', 'extension://system/assets/tmpl/finder.main.razr.php');
            $event->register('finder.table', 'extension://system/assets/tmpl/finder.table.razr.php');
            $event->register('finder.thumbnail', 'extension://system/assets/tmpl/finder.thumbnail.razr.php');
            $event->register('link.types', 'extension://system/assets/tmpl/link.types.razr.php');
            $event->register('linkpicker.modal', 'extension://system/assets/tmpl/linkpicker.modal.razr.php');
            $event->register('linkpicker.replace', 'extension://system/assets/tmpl/linkpicker.replace.razr.php');
            $event->register('marketplace.details', 'extension://system/assets/tmpl/marketplace.details.razr.php');
            $event->register('marketplace.table', 'extension://system/assets/tmpl/marketplace.table.razr.php');
            $event->register('package.updates', 'extension://system/assets/tmpl/package.updates.razr.php');
            $event->register('package.upload', 'extension://system/assets/tmpl/package.upload.razr.php');
        });
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
     * Check if the administration area is currently active.
     *
     * return bool
     */
    public function isAdmin()
    {
        return (bool) $this('request')->attributes->get('_route_options')->get('admin', false);
    }

    /**
     * Clear cache on kernel terminate event.
     */
    public function clearCache(array $options = array())
    {
        $self = $this;

        $this('router')->finish(function() use ($self, $options) {
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
