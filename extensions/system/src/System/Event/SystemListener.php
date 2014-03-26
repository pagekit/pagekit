<?php

namespace Pagekit\System\Event;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Content\MarkdownEditor;
use Pagekit\Content\Plugin\LinkPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\UserWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\EditorHelper;
use Pagekit\System\Templating\FinderHelper;
use Pagekit\System\Widget\LoginWidget;
use Pagekit\System\Widget\TextWidget;
use Pagekit\User\Auth\UserProvider;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Event\PermissionEvent;
use Razr\SimpleFilter;
use Razr\SimpleFunction;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SystemListener extends EventSubscriber
{
    /**
     * Sets admin based on the current path info.
     */
    public function onEarlyKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        self::$app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $event->getRequest()->getPathInfo());
    }

    /**
     * Dispatches init events.
     */
    public function onInitKernelRequest(GetResponseEvent $event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->dispatch('init');
        $dispatcher->dispatch($this('isAdmin') ? 'admin.init' : 'site.init');
    }

    /**
     * Deactivate extension on load failure.
     *
     * @param LoadFailureEvent $event
     */
    public function onExtensionLoadException(LoadFailureEvent $event)
    {
        $extensions = $this('option')->get('system:extensions', array());

        if (false !== $index = array_search($event->getExtensionName(), $extensions)) {
            unset($extensions[$index]);
            $extensions = array_values($extensions);
        }

        $this('option')->set('system:extensions', $extensions);
    }

    /**
     * Initialize system.
     *
     * @param InitEvent $event
     */
    public function onInit()
    {
        $app = $this('app');

        $scripts = $app['system']->getConfig('view.scripts');
        $scripts($app['view.scripts']);

        $helper = new DateHelper($app['dates']);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('date', array($helper, 'format')));

        $helper = new EditorHelper($app);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('editor', array($helper, 'render')));

        $helper = new FinderHelper($app);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('finder', array($helper, 'render')));

        $app['auth']->setUserProvider(new UserProvider($app['auth.encoder.native']));
        $app['auth']->refresh($app['option']->get(UserListener::REFRESH_TOKEN));

        $app['widgets']->registerType(new LoginWidget);
        $app['widgets']->registerType(new MenuWidget);
        $app['widgets']->registerType(new TextWidget);

        $app['events']->addSubscriber(new LinkPlugin);
        $app['events']->addSubscriber(new VideoPlugin);
        $app['events']->addSubscriber(new SimplePlugin);
        $app['events']->addSubscriber(new MarkdownEditor);

        $app['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
        $app['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
        $app['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
        $app['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        $app['view']->addAction('head', function() use ($app) {

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
    }

    /**
     * Registers dashboard widgets.
     *
     * @param DashboardEvent $event
     */
    public function onSystemDashboard(DashboardEvent $event)
    {
        $event->registerType(new FeedWidget);
        $event->registerType(new UserWidget);
        $event->registerType(new WeatherWidget);
    }

    /**
     * Registers locales.
     *
     * @param LocaleEvent $event
     */
    public function onSystemLocale(LocaleEvent $event)
    {
        $event->addMessages(array(

            'short'       => __('DATE_SHORT'),
            'medium'      => __('DATE_MEDIUM'),
            'long'        => __('DATE_LONG'),
            'full'        => __('DATE_FULL'),
            'shortdays'   => array(__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')),
            'longdays'    => array(__('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')),
            'shortmonths' => array(__('Jan'), __('Feb'), __('Mar'), __('Apr'), __('May'), __('Jun'), __('Jul'), __('Aug'), __('Sep'), __('Oct'), __('Nov'), __('Dec')),
            'longmonths'  => array(__('January'), __('February'), __('March'), __('April'), __('May'), __('June'), __('July'), __('August'), __('September'), __('October'), __('November'), __('December'))

        ), 'date');
    }

    /**
     * Registers links.
     *
     * @param LinkEvent $event
     */
    public function onSystemLink(LinkEvent $event)
    {
        $event->register('Pagekit\System\Link\Frontpage');
        $event->register('Pagekit\System\Link\Url');
    }

    /**
     * Registers templates.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('feed.error', 'extension://system/assets/tmpl/feed.error.razr.php');
        $event->register('feed.list', 'extension://system/assets/tmpl/feed.list.razr.php');
        $event->register('finder.main', 'extension://system/assets/tmpl/finder.main.razr.php');
        $event->register('finder.table', 'extension://system/assets/tmpl/finder.table.razr.php');
        $event->register('finder.thumbnail', 'extension://system/assets/tmpl/finder.thumbnail.razr.php');
        $event->register('linkpicker.modal', 'extension://system/assets/tmpl/linkpicker.modal.razr.php');
        $event->register('linkpicker.replace', 'extension://system/assets/tmpl/linkpicker.replace.razr.php');
        $event->register('marketplace.details', 'extension://system/assets/tmpl/marketplace.details.razr.php');
        $event->register('marketplace.table', 'extension://system/assets/tmpl/marketplace.table.razr.php');
        $event->register('package.updates', 'extension://system/assets/tmpl/package.updates.razr.php');
        $event->register('package.upload', 'extension://system/assets/tmpl/package.upload.razr.php');
    }

    /**
     * Registers permissions.
     *
     * @param PermissionEvent $event
     */
    public function onSystemPermission(PermissionEvent $event)
    {
        $event->setPermissions('system', array(

            'system: manage menus' => array(
                'title' => __('Manage menus')
            ),
            'system: manage widgets' => array(
                'title' => __('Manage widgets')
            ),
            'system: manage themes' => array(
                'title' => __('Manage themes')
            ),
            'system: manage extensions' => array(
                'title' => __('Manage extensions')
            ),
            'system: manage url aliases' => array(
                'title' => __('Manage url aliases')
            ),
            'system: manage users' => array(
                'title' => __('Manage users'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: manage user permissions' => array(
                'title' => __('Manage user permissions'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: access admin area' => array(
                'title' => __('Access admin area'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: access settings' => array(
                'title' => __('Access system settings'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: software updates' => array(
                'title' => __('Apply system updates'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: manage storage' => array(
                'title' => __('Manage storage'),
                'description' => __('Warning: Give to trusted roles only; this permission has security implications.')
            ),
            'system: maintenance access' => array(
                'title' => __('Use the site in maintenance mode')
            )

        ));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array(
                array('onEarlyKernelRequest', 256),
                array('onInitKernelRequest', 64)
            ),
            'init'                   => 'onInit',
            'system.link'            => 'onSystemLink',
            'system.tmpl'            => 'onSystemTmpl',
            'system.locale'          => 'onSystemLocale',
            'system.permission'      => 'onSystemPermission',
            'system.dashboard'       => 'onSystemDashboard',
            'extension.load_failure' => 'onExtensionLoadException'
        );
    }
}
