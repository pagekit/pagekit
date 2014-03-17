<?php

namespace Pagekit;

use Pagekit\Framework\Application;
use Pagekit\Framework\ServiceProviderInterface;
use Pagekit\Package\Event\LoadFailureEvent;
use Pagekit\Package\Exception\ExtensionLoadException;
use Pagekit\Package\ExtensionManager;
use Pagekit\Package\ThemeManager;
use Pagekit\Package\Loader\ExtensionLoader;
use Pagekit\Package\Loader\ThemeLoader;
use Pagekit\Package\Repository\ExtensionRepository;
use Pagekit\Package\Repository\ThemeRepository;
use Pagekit\System\FileProvider;
use Pagekit\Component\Package\Installer\PackageInstaller;
use Pagekit\Component\View\View;
use Pagekit\Component\View\Event\ActionEvent;

class SystemServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
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
        if ($app->runningInConsole()) {
            $app->on('console.init', function($event) {

                $console = $event->getConsole();
                $namespace = 'Pagekit\\System\\Console\\';

                foreach (glob(__DIR__.'/System/Console/*Command.php') as $file) {
                    $class = $namespace.basename($file, '.php');
                    $console->add(new $class);
                }

            });
        }

        foreach (array_unique($app['extensions.boot']) as $extension) {
            try {
                $app['extensions']->load($extension)->boot($app);
            } catch (ExtensionLoadException $e) {
                $app['events']->dispatch('extension.load_failure', new LoadFailureEvent($extension));
            }
        }
    }
}
