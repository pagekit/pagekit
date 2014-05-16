<?php

namespace Pagekit;

use Pagekit\Component\File\Exception\InvalidArgumentException;
use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Component\View\Event\ActionEvent;
use Pagekit\Component\View\View;
use Pagekit\Extension\ExtensionManager;
use Pagekit\Extension\Event\ExtensionListener;
use Pagekit\Extension\Package\ExtensionLoader;
use Pagekit\Extension\Package\ExtensionRepository;
use Pagekit\Framework\Application;
use Pagekit\Framework\Event\EventSubscriberInterface;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\System\FileProvider;

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
        if ($app->runningInConsole()) {

            $app['isAdmin'] = false;

            $app['events']->dispatch('system.init');
            $app['events']->addListener('console.init', function($event) {

                $console = $event->getConsole();
                $namespace = 'Pagekit\\System\\Console\\';

                foreach (glob(__DIR__.'/System/Console/*Command.php') as $file) {
                    $class = $namespace.basename($file, '.php');
                    $console->add(new $class);
                }

            });
        }

        $app['events']->addSubscriber($this);
        $app['events']->addSubscriber(new ExtensionListener);
    }

    public function onKernelRequest($event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->app['isAdmin'] = (bool) preg_match('#^/admin(/?$|/.+)#', $event->getRequest()->getPathInfo());

        $dispatcher->dispatch('system.init', $event);
    }

    public function onRequestMatched($event, $name, $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->dispatch('system.loaded', $event);
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
                array('onKernelRequest', 50),
                array('onRequestMatched', 0)
            ),
            'templating.reference' => 'onTemplateReference'
        );
    }
}
