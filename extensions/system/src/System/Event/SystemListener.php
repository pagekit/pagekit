<?php

namespace Pagekit\System\Event;

use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Content\Plugin\LinkPlugin;
use Pagekit\Content\Plugin\MarkdownPlugin;
use Pagekit\Content\Plugin\SimplePlugin;
use Pagekit\Content\Plugin\VideoPlugin;
use Pagekit\Editor\MarkdownEditor;
use Pagekit\Editor\Plugin\ImagePlugin as EditorImagePlugin;
use Pagekit\Editor\Plugin\LinkPlugin as EditorLinkPlugin;
use Pagekit\Editor\Plugin\VideoPlugin as EditorVideoPlugin;
use Pagekit\Framework\Event\EventSubscriber;
use Pagekit\Menu\Widget\MenuWidget;
use Pagekit\System\Dashboard\FeedWidget;
use Pagekit\System\Dashboard\WeatherWidget;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Templating\DateHelper;
use Pagekit\System\Templating\EditorHelper;
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
    public function onInit()
    {
        $app = $this('app');

        $app['view.scripts']->register('jquery', 'vendor://assets/jquery/jquery.js', array(), array('requirejs' => true));
        $app['view.scripts']->register('requirejs', 'vendor://assets/requirejs/require.min.js', array('requirejs-config'));
        $app['view.scripts']->register('requirejs-config', 'asset://system/js/require.js');
        $app['view.scripts']->register('uikit', 'vendor://assets/uikit/js/uikit.min.js', array(), array('requirejs' => true));
        $app['view.scripts']->register('uikit-notify', 'vendor://assets/uikit/js/addons/notify.js', array(), array('requirejs' => true));

        $helper = new DateHelper($app['dates']);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('date', array($helper, 'format')));

        $helper = new EditorHelper($app);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('editor', array($helper, 'render')));

        $helper = new FinderHelper($app);
        $app['tmpl.php']->addHelpers(array($helper));
        $app['tmpl.razr']->getEnvironment()->addFunction(new SimpleFunction('finder', array($helper, 'render')));

        $app['tmpl.razr']->getEnvironment()->addFilter(new SimpleFilter('urldecode', 'urldecode'));

        $app['auth']->setUserProvider(new UserProvider($app['auth.encoder.native']));
        $app['auth']->refresh($app['option']->get(UserListener::REFRESH_TOKEN));

        $app['events']->addSubscriber(new LinkPlugin);
        $app['events']->addSubscriber(new MarkdownPlugin);
        $app['events']->addSubscriber(new SimplePlugin);
        $app['events']->addSubscriber(new VideoPlugin);

        $app['events']->addSubscriber(new EditorImagePlugin);
        $app['events']->addSubscriber(new EditorLinkPlugin);
        $app['events']->addSubscriber(new EditorVideoPlugin);
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
        $event->register('Pagekit\System\Link\Frontpage');
        $event->register('Pagekit\System\Link\Url');
        $event->register('Pagekit\User\Link\Login');
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
            'init'                   => 'onInit',
            'system.link'            => 'onSystemLink',
            'system.tmpl'            => 'onSystemTmpl',
            'system.locale'          => 'onSystemLocale',
            'system.dashboard'       => 'onSystemDashboard',
            'system.widget'          => 'onSystemWidget',
            'extension.load_failure' => 'onExtensionLoadException'
        );
    }
}
