<?php

namespace Pagekit\Routing\Controller;

use Composer\Autoload\ClassLoader;
use Pagekit\Routing\Event\RouteCollectionEvent;
use Pagekit\Routing\Event\RouteResourcesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;

class ControllerCollection implements EventSubscriberInterface
{
    protected $reader;
    protected $loader;
    protected $debug;
    protected $routes = [];

    /**
     * Constructor.
     *
     * @param ControllerReaderInterface $reader
     * @param ClassLoader               $loader
     */
    public function __construct(ControllerReaderInterface $reader, ClassLoader $loader, $debug = false)
    {
        $this->reader = $reader;
        $this->loader = $loader;
        $this->debug  = $debug;
    }

    /**
     * Mounts controllers under given prefix and namespace
     *
     * @param string          $prefix
     * @param string|string[] $controllers
     * @param string          $namespace
     * @param array           $defaults
     * @param array           $requirements
     */
    public function mount($prefix, $controllers, $namespace, array $defaults = [], array $requirements = [])
    {
        $this->routes[] = new Route($prefix, $defaults, $requirements, compact('namespace', 'controllers'));
    }

    /**
     * Adds this instances routes to the collection.
     *
     * @param RouteCollectionEvent $event
     */
    public function getRoutes(RouteCollectionEvent $event)
    {
        $routes = $event->getRoutes();
        foreach ($this->routes as $route) {
            foreach ((array) $route->getOption('controllers') as $controller) {
                try {

                    foreach ($this->reader->read($controller) as $name => $r) {
                        $routes->add(trim($route->getOption('namespace').'/'.$name, '/'), $r
                            ->setPath(rtrim($route->getPath().$r->getPath(), '/'))
                            ->addDefaults($route->getDefaults())
                            ->addRequirements($route->getRequirements())
                        );
                    }

                } catch (\InvalidArgumentException $e) {

                    if ($this->debug) {
                        throw $e;
                    }

                }
            }
        }
    }

    /**
     * Adds this instances resources to the collection
     *
     * @param RouteResourcesEvent $event
     */
    public function getResources(RouteResourcesEvent $event)
    {
        $resources = [];
        foreach ($this->routes as $route) {
            foreach ((array) $route->getOption('controllers') as $controller) {
                if (is_string($controller) && $file = $this->loader->findFile($controller)) {
                    $resources[] = ['file' => $file, 'prefix' => $route->getPath(), 'namespace' => $route->getOption('namespace')];
                }
            }
        }
        $event->addResources($resources);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'route.collection' => ['getRoutes', 8],
            'route.resources'  => 'getResources'
        ];
    }
}
