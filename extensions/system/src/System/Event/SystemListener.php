<?php

namespace Pagekit\System\Event;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Editor\Editor;
use Pagekit\Editor\Templating\EditorHelper;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\Razr\Directive\FunctionDirective;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\FinderHelper;
use Pagekit\System\Widget\TextWidget;
use Pagekit\User\Auth\UserProvider;
use Pagekit\User\Dashboard\UserWidget;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Widget\LoginWidget;
use Pagekit\Widget\Event\RegisterWidgetEvent;
use Razr\SimpleFilter;
use Razr\SimpleFunction;

class SystemListener extends EventSubscriber
{
    /**
     * Initialize system.
     */
    public function onSystemInit()
    {
        $app = $this('app');

        $scripts = $app['view.scripts'];
        $scripts->register('jquery', 'vendor://assets/jquery/jquery.js', array(), array('requirejs' => true));
        $scripts->register('requirejs', 'asset://system/js/require.min.js', array('requirejs-config'));
        $scripts->register('requirejs-config', 'asset://system/js/require.js');
        $scripts->register('uikit', 'vendor://assets/uikit/js/uikit.min.js', array(), array('requirejs' => true));
        $scripts->register('uikit-notify', 'vendor://assets/uikit/js/addons/notify.js', array(), array('requirejs' => true));
        $scripts->register('uikit-sticky', 'vendor://assets/uikit/js/addons/sticky.js', array(), array('requirejs' => true));
        $scripts->register('uikit-sortable', 'vendor://assets/uikit/js/addons/sortable.js', array(), array('requirejs' => true));

        $helper = new DateHelper($app['dates']);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('date', array($helper, 'format')));
        $app['tmpl.razr2']->addDirective(new FunctionDirective('date', array($helper, 'format')));
        $app['tmpl.razr2']->addFunction('date', array($helper, 'format'));

        $helper = new EditorHelper($app['events']);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('editor', array($helper, 'render')));
        $app['tmpl.razr2']->addDirective(new FunctionDirective('editor', array($helper, 'render')));

        $helper = new FinderHelper($app);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('finder', array($helper, 'render')));
        $app['tmpl.razr2']->addDirective(new FunctionDirective('finder', array($helper, 'render')));

        $app['tmpl.razr2']->addDirective(new FunctionDirective('url_route', array($app['url'], 'route')));
        $app['tmpl.razr2']->addDirective(new FunctionDirective('url_to', array($app['url'], 'to')));

        $app['tmpl.razr2']->addFunction('url_route', array($app['url'], 'route'));
        $app['tmpl.razr2']->addFunction('url_to', array($app['url'], 'to'));

        $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('urldecode', 'urldecode'));

        $app['auth']->setUserProvider(new UserProvider($app['auth.password']));
        $app['auth']->refresh($app['option']->get(UserListener::REFRESH_TOKEN));

        $app['events']->addSubscriber(new Editor);
        $app['events']->addSubscriber(new MarkdownPlugin);
        $app['events']->addSubscriber(new SimplePlugin);
        $app['events']->addSubscriber(new VideoPlugin);

        $app['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
        $app['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
        $app['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
        $app['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        $app['view']->addAction('head', function() use ($scripts) {

            foreach ($scripts as $script) {

                $dependencies = (array) $script['dependencies'];

                if (isset($script['requirejs'])) {
                    $script['dependencies'] = array_merge($dependencies, array('requirejs'));
                } elseif (in_array('requirejs', $dependencies)) {
                    $scripts->dequeue($name = $script->getName());
                    $scripts->queue($name);
                }
            }

        }, 5);

        $app['view']->addAction('messages', function(ActionEvent $event) use ($app) {
            $event->append($app['view']->render('system/messages/messages.razr'));
        });
    }

    /**
     * Dispatches the 'system.site' or 'system.admin' event.
     */
    public function onSystemLoaded($event, $name, $dispatcher)
    {
        $dispatcher->dispatch($this('isAdmin') ? 'system.admin' : 'system.site', $event);
    }

    /**
     * Creates the menu instance and dispatches the 'system.admin_menu' event.
     */
    public function onSystemAdmin()
    {
        $menu = new Menu;
        $menu->setId('admin');

        $this('menus')->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        $this('menus')->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        $this('events')->dispatch('system.admin_menu', new MenuEvent($menu));

        self::$app['admin.menu'] = $this('menus')->getTree($menu, array('access' => true));
    }

    /**
     * Registers widgets.
     *
     * @param RegisterWidgetEvent $event
     */
    public function onSystemWidget(RegisterWidgetEvent $event)
    {
        $event->register(new LoginWidget);
        $event->register(new MenuWidget);
        $event->register(new TextWidget);
    }

    /**
     * Registers dashboard widgets.
     *
     * @param RegisterWidgetEvent $event
     */
    public function onSystemDashboard(RegisterWidgetEvent $event)
    {
        $event->register(new FeedWidget);
        $event->register(new UserWidget);
        $event->register(new WeatherWidget);
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
        if (!in_array($event->getContext(), ['frontpage', 'urlalias'])) {
            $event->register('Pagekit\System\Link\Frontpage');
        }
        $event->register('Pagekit\User\Link\User');
    }

    /**
     * Registers templates.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('feed.error', 'view://system/tmpl/feed.error.razr');
        $event->register('feed.list', 'view://system/tmpl/feed.list.razr');
        $event->register('finder.main', 'view://system/tmpl/finder.main.razr');
        $event->register('finder.table', 'view://system/tmpl/finder.table.razr');
        $event->register('finder.thumbnail', 'view://system/tmpl/finder.thumbnail.razr');
        $event->register('linkpicker.modal', 'view://system/tmpl/linkpicker.modal.razr');
        $event->register('linkpicker.replace', 'view://system/tmpl/linkpicker.replace.razr');
        $event->register('marketplace.details', 'view://system/tmpl/marketplace.details.razr');
        $event->register('marketplace.table', 'view://system/tmpl/marketplace.table.razr');
        $event->register('package.updates', 'view://system/tmpl/package.updates.razr');
        $event->register('package.upload', 'view://system/tmpl/package.upload.razr');
    }

    /**
     * Registers the media storage folder
     *
     * @param FileAccessEvent $event
     */
    public function onSystemFinder(FileAccessEvent $event)
    {
        if ($this('user')->hasAccess('system: manage storage | system: manage storage read only')) {
            $mode = $this('user')->hasAccess('system: manage storage') ? 'w' : 'r';
            $event->path('/^'.preg_quote($this('path').$this('config')->get('app.storage'), '/').'($|\/.*)/', $mode);
        }
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'system.init'            => array('onSystemInit', 20),
            'system.loaded'          => 'onSystemLoaded',
            'system.admin'           => 'onSystemAdmin',
            'system.link'            => 'onSystemLink',
            'system.tmpl'            => 'onSystemTmpl',
            'system.locale'          => 'onSystemLocale',
            'system.dashboard'       => 'onSystemDashboard',
            'system.widget'          => 'onSystemWidget',
            'system.finder'          => 'onSystemFinder',
            'extension.load_failure' => 'onExtensionLoadException'
        );
    }
}
