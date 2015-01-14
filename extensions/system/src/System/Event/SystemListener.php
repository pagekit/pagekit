<?php

namespace Pagekit\System\Event;

use Pagekit\Comment\CommentPlugin;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Editor\Editor;
use Pagekit\Editor\Templating\EditorHelper;
use Pagekit\Framework\Application as App;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\FinderHelper;
use Pagekit\System\Widget\TextWidget;
use Pagekit\User\Dashboard\UserWidget;
use Pagekit\User\Event\PermissionEvent;
use Pagekit\User\Widget\LoginWidget;
use Pagekit\Widget\Event\RegisterWidgetEvent;
use Razr\Directive\FunctionDirective;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SystemListener implements EventSubscriberInterface
{
    /**
     * Dispatches the 'system.site' or 'system.admin' event.
     */
    public function onSystemLoaded($event, $name, $dispatcher)
    {
        $scripts = App::get('view.scripts');
        $scripts->register('jquery', 'vendor/assets/jquery/jquery.js', [], ['requirejs' => true]);
        $scripts->register('requirejs', 'extensions/system/assets/js/require.min.js', ['requirejs-config']);
        $scripts->register('requirejs-config', 'extensions/system/assets/js/require.js');
        $scripts->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', [], ['requirejs' => true]);
        $scripts->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', [], ['requirejs' => true]);

        $helper = new DateHelper(App::dates());
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('date', [$helper, 'format']));
        App::get('tmpl.razr')->addFunction('date', [$helper, 'format']);

        $helper = new EditorHelper(App::events());
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('editor', [$helper, 'render']));

        $helper = new FinderHelper(App::getInstance());
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('finder', [$helper, 'render']));

        App::events()->addSubscriber(new CommentPlugin);
        App::events()->addSubscriber(new Editor);
        App::events()->addSubscriber(new MarkdownPlugin);
        App::events()->addSubscriber(new SimplePlugin);
        App::events()->addSubscriber(new VideoPlugin);

        App::menus()->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
        App::menus()->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
        App::menus()->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
        App::menus()->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        App::get('view.sections')->set('messages', function() {
            return App::view()->render('extensions/system/views/messages/messages.razr');
        });

        $dispatcher->dispatch(App::isAdmin() ? 'system.admin' : 'system.site', $event);
    }

    /**
     * Creates the menu instance and dispatches the 'system.admin_menu' event.
     */
    public function onSystemAdmin()
    {
        $menu = new Menu;
        $menu->setId('admin');

        App::menus()->registerFilter('access', 'Pagekit\System\Menu\Filter\AccessFilter', 16);
        App::menus()->registerFilter('active', 'Pagekit\System\Menu\Filter\ActiveFilter');

        App::events()->dispatch('system.admin_menu', new MenuEvent($menu));

        App::set('admin.menu', App::menus()->getTree($menu, ['access' => true]));
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
        $event->register('feed.error', 'extensions/system/views/tmpl/feed.error.razr');
        $event->register('feed.list', 'extensions/system/views/tmpl/feed.list.razr');
        $event->register('finder.main', 'extensions/system/views/tmpl/finder.main.razr');
        $event->register('finder.table', 'extensions/system/views/tmpl/finder.table.razr');
        $event->register('finder.thumbnail', 'extensions/system/views/tmpl/finder.thumbnail.razr');
        $event->register('linkpicker.modal', 'extensions/system/views/tmpl/linkpicker.modal.razr');
        $event->register('linkpicker.replace', 'extensions/system/views/tmpl/linkpicker.replace.razr');
        $event->register('marketplace.details', 'extensions/system/views/tmpl/marketplace.details.razr');
        $event->register('marketplace.table', 'extensions/system/views/tmpl/marketplace.table.razr');
        $event->register('package.updates', 'extensions/system/views/tmpl/package.updates.razr');
        $event->register('package.upload', 'extensions/system/views/tmpl/package.upload.razr');
        $event->register('settings.oauth', 'extensions/system/views/tmpl/settings.oauth.razr');
    }

    /**
     * Registers the media storage folder
     *
     * @param FileAccessEvent $event
     */
    public function onSystemFinder(FileAccessEvent $event)
    {
        if (App::user()->hasAccess('system: manage storage | system: manage storage read only')) {
            $event->path('#^'.strtr(App::get('path.storage'), '\\', '/').'($|\/.*)#', App::user()->hasAccess('system: manage storage') ? 'w' : 'r');
        }
    }

    /**
     * Deactivate extension on load failure.
     *
     * @param LoadFailureEvent $event
     */
    public function onExtensionLoadException(LoadFailureEvent $event)
    {
        $extensions = App::option()->get('system:extensions', []);

        if (false !== $index = array_search($event->getExtensionName(), $extensions)) {
            unset($extensions[$index]);
            $extensions = array_values($extensions);
        }

        App::option()->set('system:extensions', $extensions);
    }

    /**
     * Registers the extension permissions
     *
     * @param PermissionEvent $event
     */
    public function onSystemPermission(PermissionEvent $event)
    {
        foreach (App::extensions() as $extension) {
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
