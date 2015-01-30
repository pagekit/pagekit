<?php

namespace Pagekit\System\Event;

use Pagekit\Application as App;
use Pagekit\Menu\Event\MenuEvent;
use Pagekit\Menu\Model\Menu;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\FinderHelper;
use Pagekit\System\Widget\TextWidget;
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

        $helper = new FinderHelper();
        App::get('tmpl.php')->addHelpers([$helper]);
        App::get('tmpl.razr')->addDirective(new FunctionDirective('finder', [$helper, 'render']));

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
        $event->register(new MenuWidget);
        $event->register(new TextWidget);
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
        $event->register('finder.main', 'extensions/system/views/tmpl/finder.main.razr');
        $event->register('finder.table', 'extensions/system/views/tmpl/finder.table.razr');
        $event->register('finder.thumbnail', 'extensions/system/views/tmpl/finder.thumbnail.razr');
        $event->register('linkpicker.modal', 'extensions/system/views/tmpl/linkpicker.modal.razr');
        $event->register('linkpicker.replace', 'extensions/system/views/tmpl/linkpicker.replace.razr');
        $event->register('marketplace.details', 'extensions/system/views/tmpl/marketplace.details.razr');
        $event->register('marketplace.table', 'extensions/system/views/tmpl/marketplace.table.razr');
        $event->register('package.updates', 'extensions/system/views/tmpl/package.updates.razr');
        $event->register('package.upload', 'extensions/system/views/tmpl/package.upload.razr');
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'system.admin'  => 'onSystemAdmin',
            'system.finder' => 'onSystemFinder',
            'system.link'   => 'onSystemLink',
            'system.loaded' => 'onSystemLoaded',
            'system.tmpl'   => 'onSystemTmpl',
            'system.widget' => 'onSystemWidget'
        ];
    }
}
