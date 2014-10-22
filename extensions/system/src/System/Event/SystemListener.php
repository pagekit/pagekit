<?php

namespace Pagekit\System\Event;

use Pagekit\Comment\CommentPlugin;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Editor\Editor;
use Pagekit\Editor\Templating\EditorHelper;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\FinderHelper;
use Pagekit\System\Widget\TextWidget;
use Pagekit\User\Auth\UserProvider;
use Pagekit\User\Dashboard\UserWidget;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Event\UserListener;
use Pagekit\User\Widget\LoginWidget;
use Pagekit\Widget\Event\RegisterWidgetEvent;
use Razr\Directive\FunctionDirective;

class SystemListener extends EventSubscriber
{
    /**
     * Initialize system.
     */
    public function onSystemInit()
    {
        $this['auth']->setUserProvider(new UserProvider($this['auth.password']));
        $this['auth']->refresh($this['option']->get(UserListener::REFRESH_TOKEN));
    }

    /**
     * Dispatches the 'system.site' or 'system.admin' event.
     */
    public function onSystemLoaded($event, $name, $dispatcher)
    {
        $scripts = $this['view.scripts'];
        $scripts->register('jquery', 'vendor://assets/jquery/jquery.js', [], ['requirejs' => true]);
        $scripts->register('requirejs', 'extension://system/assets/js/require.min.js', ['requirejs-config']);
        $scripts->register('requirejs-config', 'extension://system/assets/js/require.js');
        $scripts->register('uikit', 'vendor://assets/uikit/js/uikit.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-notify', 'vendor://assets/uikit/js/components/notify.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-sticky', 'vendor://assets/uikit/js/components/sticky.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-sortable', 'vendor://assets/uikit/js/components/sortable.min.js', [], ['requirejs' => true]);

        $helper = new DateHelper($this['dates']);
        $this['tmpl.php']->addHelpers([$helper]);
        $this['tmpl.razr']->addDirective(new FunctionDirective('date', [$helper, 'format']));
        $this['tmpl.razr']->addFunction('date', [$helper, 'format']);

        $helper = new EditorHelper($this['events']);
        $this['tmpl.php']->addHelpers([$helper]);
        $this['tmpl.razr']->addDirective(new FunctionDirective('editor', [$helper, 'render']));

        $helper = new FinderHelper($this->getApplication());
        $this['tmpl.php']->addHelpers([$helper]);
        $this['tmpl.razr']->addDirective(new FunctionDirective('finder', [$helper, 'render']));

        $this['events']->addSubscriber(new CommentPlugin);
        $this['events']->addSubscriber(new Editor);
        $this['events']->addSubscriber(new MarkdownPlugin);
        $this['events']->addSubscriber(new SimplePlugin);
        $this['events']->addSubscriber(new VideoPlugin);

        $this['menus']->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
        $this['menus']->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
        $this['menus']->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
        $this['menus']->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        $this['view.sections']->set('messages', function() {
            return $this['view']->render('extension://system/views/messages/messages.razr');
        });

        $dispatcher->dispatch($this['isAdmin'] ? 'system.admin' : 'system.site', $event);
    }

    /**
     * Creates the menu instance and dispatches the 'system.admin_menu' event.
     */
    public function onSystemAdmin()
    {
        $menu = new Menu;
        $menu->setId('admin');

        $this['menus']->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        $this['menus']->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        $this['events']->dispatch('system.admin_menu', new MenuEvent($menu));

        $this['admin.menu'] = $this['menus']->getTree($menu, ['access' => true]);
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
        $event->addMessages([

            'short'       => __('DATE_SHORT'),
            'medium'      => __('DATE_MEDIUM'),
            'long'        => __('DATE_LONG'),
            'full'        => __('DATE_FULL'),
            'shortdays'   => [__('Mon'), __('Tue'), __('Wed'), __('Thu'), __('Fri'), __('Sat'), __('Sun')],
            'longdays'    => [__('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')],
            'shortmonths' => [__('Jan'), __('Feb'), __('Mar'), __('Apr'), __('May'), __('Jun'), __('Jul'), __('Aug'), __('Sep'), __('Oct'), __('Nov'), __('Dec')],
            'longmonths'  => [__('January'), __('February'), __('March'), __('April'), __('May'), __('June'), __('July'), __('August'), __('September'), __('October'), __('November'), __('December')]

        ], 'date');
    }

    /**
     * Registers links.
     *
     * @param LinkEvent $event
     */
    public function onSystemLink(LinkEvent $event)
    {
        $event->register('Pagekit\System\Link\System');
    }

    /**
     * Registers templates.
     *
     * @param TmplEvent $event
     */
    public function onSystemTmpl(TmplEvent $event)
    {
        $event->register('feed.error', 'extension://system/views/tmpl/feed.error.razr');
        $event->register('feed.list', 'extension://system/views/tmpl/feed.list.razr');
        $event->register('finder.main', 'extension://system/views/tmpl/finder.main.razr');
        $event->register('finder.table', 'extension://system/views/tmpl/finder.table.razr');
        $event->register('finder.thumbnail', 'extension://system/views/tmpl/finder.thumbnail.razr');
        $event->register('linkpicker.modal', 'extension://system/views/tmpl/linkpicker.modal.razr');
        $event->register('linkpicker.replace', 'extension://system/views/tmpl/linkpicker.replace.razr');
        $event->register('marketplace.details', 'extension://system/views/tmpl/marketplace.details.razr');
        $event->register('marketplace.table', 'extension://system/views/tmpl/marketplace.table.razr');
        $event->register('package.updates', 'extension://system/views/tmpl/package.updates.razr');
        $event->register('package.upload', 'extension://system/views/tmpl/package.upload.razr');
        $event->register('settings.oauth', 'extension://system/views/tmpl/settings.oauth.razr');
    }

    /**
     * Registers the media storage folder
     *
     * @param FileAccessEvent $event
     */
    public function onSystemFinder(FileAccessEvent $event)
    {
        if ($this['user']->hasAccess('system: manage storage | system: manage storage read only')) {
            $event->path('#^'.strtr($this['path.storage'], '\\', '/').'($|\/.*)#', $this['user']->hasAccess('system: manage storage') ? 'w' : 'r');
        }
    }

    /**
     * Deactivate extension on load failure.
     *
     * @param LoadFailureEvent $event
     */
    public function onExtensionLoadException(LoadFailureEvent $event)
    {
        $extensions = $this['option']->get('system:extensions', []);

        if (false !== $index = array_search($event->getExtensionName(), $extensions)) {
            unset($extensions[$index]);
            $extensions = array_values($extensions);
        }

        $this['option']->set('system:extensions', $extensions);
    }

    /**
     * Registers the extension permissions
     *
     * @param PermissionEvent $event
     */
    public function onSystemPermission(PermissionEvent $event)
    {
        foreach ($this['extensions'] as $extension) {
            if ($permissions = $extension->getConfig('permissions')) {
                $event->setPermissions($extension->getName(), $permissions);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.admin'           => 'onSystemAdmin',
            'system.dashboard'       => 'onSystemDashboard',
            'system.finder'          => 'onSystemFinder',
            'system.init'            => ['onSystemInit', 20],
            'system.link'            => 'onSystemLink',
            'system.loaded'          => 'onSystemLoaded',
            'system.locale'          => 'onSystemLocale',
            'system.permission'      => 'onSystemPermission',
            'system.tmpl'            => 'onSystemTmpl',
            'system.widget'          => 'onSystemWidget',
            'extension.load_failure' => 'onExtensionLoadException'
        ];
    }
}
