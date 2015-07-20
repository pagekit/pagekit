<?php

namespace Pagekit\Routing\Loader;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Routing\Route;
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

        foreach ($routes as $route) {

            if ($route->getOption('controller')) {

                foreach ((array) $route->getOption('controller') as $controller) {

                    if (is_string($controller) && class_exists($controller)) {
                        $this->addController($route, $controller);
                    } else {
                        $this->addRoute($route);
                    }

                }

            } else {
                
                $this->addRoute($route);

            }

        }

        return $this->routes;
    }

    /**
     * Adds a route.
     *
     * @param Route $route
     */
    protected function addRoute($route)
    {
        $this->routes->add($route->getName(), $route);
        $this->events->trigger('route.configure', [$route, $this->routes]);
    }

    /**
     * Adds routes from controller class.
     *
     * @param Route  $route
     * @param string $controller
     */
    protected function addController($route, $controller)
    {
        try {

            foreach ($this->loader->load($controller) as $r) {

                $this->addRoute($r
                    ->setName(trim("{$route->getName()}/{$r->getName()}", "/"))
                    ->setPath(rtrim($route->getPath().$r->getPath(), '/'))
                    ->addDefaults($route->getDefaults())
                    ->addRequirements($route->getRequirements())
                );

            }

        } catch (\InvalidArgumentException $e) {}
    }
}
