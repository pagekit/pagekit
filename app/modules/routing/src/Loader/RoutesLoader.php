<?php

namespace Pagekit\Routing\Loader;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Routing\Event\ConfigureRouteEvent;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesLoader implements LoaderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * @var AnnotationLoader
     */
    protected $loader;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $events
     * @param AnnotationLoader         $loader
     */
    public function __construct(EventDispatcherInterface $events, AnnotationLoader $loader = null)
    {
        $this->events = $events;
        $this->loader = $loader ?: new AnnotationLoader();
    }

    /**
     * {@inheritdoc}
     */
    public function load($routes)
    {
        $this->routes = new RouteCollection();

        foreach ($routes as $name => $route) {
            foreach ((array) $route->getOption('controller') as $controller) {

                if (is_callable($controller)) {
                    $this->addRoute($name, $route);
                } elseif (class_exists($controller)) {
                    $this->addController($name, $route, $controller);
                }

            }
        }

        return $this->routes;
    }

    /**
     * Adds a route.
     *
     * @param string $name
     * @param Route  $route
     */
    protected function addRoute($name, $route)
    {
        if ($route = $this->events->trigger(new ConfigureRouteEvent($route))->getRoute()) {
            $this->routes->add($name, $route);
        }
    }

    /**
     * Adds routes from controller class.
     *
     * @param string $prefix
     * @param Route  $route
     * @param string $controller
     */
    protected function addController($prefix, $route, $controller)
    {
       try {

            foreach ($this->loader->load($controller) as $name => $r) {

                $this->addRoute(trim("$prefix/$name", "/"), $r
                    ->setPath(rtrim($route->getPath().$r->getPath(), '/'))
                    ->addDefaults($route->getDefaults())
                    ->addRequirements($route->getRequirements())
                );

            }

        } catch (\InvalidArgumentException $e) {}
    }
}
