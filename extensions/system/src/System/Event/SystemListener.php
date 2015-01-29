<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Editor\Editor;
use Pagekit\Editor\Templating\EditorHelper;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;
use Pagekit\Menu\Widget\MenuWidget;
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
        $scripts = App::scripts();
        $scripts->register('angular', 'vendor/assets/angular/angular.min.js', 'jquery');
        $scripts->register('angular-animate', 'vendor/assets/angular-animate/angular-animate.min.js', 'angular');
        $scripts->register('angular-cookies', 'vendor/assets/angular-cookies/angular-cookies.min.js', 'angular');
        $scripts->register('angular-loader', 'vendor/assets/angular-loader/angular-loader.min.js', 'angular');
        $scripts->register('angular-messages', 'vendor/assets/angular-messages/angular-messages.min.js', 'angular');
        $scripts->register('angular-resource', 'vendor/assets/angular-resource/angular-resource.min.js', 'angular');
        $scripts->register('angular-route', 'vendor/assets/angular-route/angular-route.min.js', 'angular');
        $scripts->register('angular-sanitize', 'vendor/assets/angular-sanitize/angular-sanitize.min.js', 'angular');
        $scripts->register('angular-touch', 'vendor/assets/angular-touch/angular-touch.min.js', 'angular');
        $scripts->register('jquery', 'vendor/assets/jquery/dist/jquery.min.js', [], ['requirejs' => true]);
        $scripts->register('requirejs', 'extensions/system/assets/js/require.min.js', ['requirejs-config']);
        $scripts->register('requirejs-config', 'extensions/system/assets/js/require.js');
        $scripts->register('uikit', 'vendor/assets/uikit/js/uikit.min.js', 'jquery', ['requirejs' => true]);
        $scripts->register('uikit-nestable', 'vendor/assets/uikit/js/components/nestable.min.js', ['uikit'], ['requirejs' => true]);
        $scripts->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', ['uikit'], ['requirejs' => true]);
        $scripts->register('uikit-sortable', 'vendor/assets/uikit/js/components/sortable.min.js', ['uikit'], ['requirejs' => true]);
        $scripts->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.min.js', ['uikit'], ['requirejs' => true]);
        $scripts->register('application', 'extensions/system/app/application.js', 'angular');
        $scripts->register('application-directives', 'extensions/system/app/directives.js', 'application');

        $helper = new DateHelper(App::dates());
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('date', [$helper, 'format']));
        App::get('tmpl.razr')->addFunction('date', [$helper, 'format']);

        $helper = new EditorHelper(App::events());
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('editor', [$helper, 'render']));

        $helper = new FinderHelper();
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('finder', [$helper, 'render']));

        App::subscribe(new Editor);
        App::subscribe(new MarkdownPlugin);
        App::subscribe(new SimplePlugin);
        App::subscribe(new VideoPlugin);

        App::menus()->registerFilter('access', 'Pagekit\Menu\Filter\AccessFilter', 16);
        App::menus()->registerFilter('status', 'Pagekit\Menu\Filter\StatusFilter', 16);
        App::menus()->registerFilter('priority', 'Pagekit\Menu\Filter\PriorityFilter');
        App::menus()->registerFilter('active', 'Pagekit\Menu\Filter\ActiveFilter');

        App::sections()->set('messages', function() {
            return App::view('extensions/system/views/messages/messages.razr');
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

        App::trigger('system.admin_menu', new MenuEvent($menu));

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
        $extensions = App::option('system:extensions', []);

        if (false !== $index = array_search($event->getExtensionName(), $extensions)) {
            unset($extensions[$index]);
            $extensions = array_values($extensions);
        }

        App::option()->set('system:extensions', $extensions);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.admin'           => 'onSystemAdmin',
            'system.finder'          => 'onSystemFinder',
            'system.link'            => 'onSystemLink',
            'system.loaded'          => 'onSystemLoaded',
            'system.locale'          => 'onSystemLocale',
            'system.tmpl'            => 'onSystemTmpl',
            'system.widget'          => 'onSystemWidget',
            'extension.load_failure' => 'onExtensionLoadException'
        ];
    }
}
