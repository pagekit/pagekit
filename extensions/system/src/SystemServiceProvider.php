<?php

namespace Pagekit;

use Pagekit\Component\File\Exception\InvalidArgumentException;
use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\View;
use Pagekit\Extension\ExtensionManager;
use Pagekit\Extension\Package\ExtensionLoader;
use Pagekit\Extension\Package\ExtensionRepository;
use Pagekit\Framework\Application;
use Pagekit\Framework\Event\EventSubscriberInterface;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\System\FileProvider;
use Pagekit\System\Package\Event\LoadFailureEvent;
use Pagekit\System\Package\Exception\ExtensionLoadException;
use Pagekit\Theme\Package\ThemeLoader;
use Pagekit\Theme\Package\ThemeRepository;
use Pagekit\Theme\ThemeManager;

class SystemServiceProvider implements ServiceProviderInterface, EventSubscriberInterface
{
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;

        $app['file'] = function($app) {
            return new FileProvider($app);
        };

        $app['view'] = function($app) {

            $view = new View($app['events'], $app['tmpl']);
            $view->set('app', $app);
            $view->set('url', $app['url']);
            $view->addAction('head', function(ActionEvent $event) use ($app) {
                $event->append(sprintf('<meta name="generator" content="Pagekit %1$s" data-version="%1$s" data-base="%2$s" />', $app['config']['app.version'], $app['url']->base() ?: '/'));
            }, 16);

            return $view;
        };

        $app['themes'] = function($app) {

            $loader     = new ThemeLoader;
            $repository = new ThemeRepository($app['config']['theme.path'], $loader);
            $installer  = new PackageInstaller($repository, $loader);

            return new ThemeManager($app, $repository, $installer, $app['autoloader'], $app['locator']);
        };

        $app['extensions'] = function($app) {

            $loader     = new ExtensionLoader;
            $repository = new ExtensionRepository($app['config']['extension.path'], $loader);
            $installer  = new PackageInstaller($repository, $loader);

            return new ExtensionManager($app, $repository, $installer, $app['autoloader'], $app['locator']);
        };

        $app['extensions.boot'] = array();
    }

    public function boot(Application $app)
    {
        foreach (array_unique($app['extensions.boot']) as $extension) {
            try {
                $app['extensions']->load($extension)->boot($app);
            } catch (ExtensionLoadException $e) {
                $app['events']->dispatch('extension.load_failure', new LoadFailureEvent($extension));
            }
        }

        if ($app->runningInConsole()) {

            $app['isAdmin'] = false;

            $app['events']->dispatch('init');
            $app['events']->on('console.init', function($event) {

                $console = $event->getConsole();
                $namespace = 'Pagekit\\System\\Console\\';

                foreach (glob(__DIR__.'/System/Console/*Command.php') as $file) {
                    $class = $namespace.basename($file, '.php');
                    $console->add(new $class);
                }

            });
        }

        $app['events']->addSubscriber($this);
    }

    public function onEarlyKernelRequest($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $event->getRequest()->getPathInfo());
    }

    public function onKernelRequest($event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->dispatch('init');
        $dispatcher->dispatch($this->app['isAdmin'] ? 'admin.init' : 'site.init');
    }

    public function onRequestMatched($event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->dispatch('loaded');
        $dispatcher->dispatch($this->app['isAdmin'] ? 'admin.loaded' : 'site.loaded');
    }

    public function onTemplateReference($event)
    {
        try {

            $template = $event->getTemplateReference();

            if (filter_var($path = $template->get('path'), FILTER_VALIDATE_URL) !== false) {
                $template->set('path', $this->app['locator']->findResource($path));
            }

        } catch (InvalidArgumentException $e) {}
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => array(
                array('onEarlyKernelRequest', 256),
                array('onKernelRequest', 64),
                array('onRequestMatched', 31)
            ),
            'templating.reference' => 'onTemplateReference'
        );
    }
}
