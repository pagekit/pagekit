<?php

namespace Pagekit\Routing\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RouteCollection;

class RouteCollectionEvent extends Event
{
    protected $routes;

    /**
     * Constructs an event.
     */
    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    /**
     * Returns the routes collection.
     *
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Adds routes.
     *
     * @param RouteCollection $routes
     */
    public function addRoutes(RouteCollection $routes)
    {
        $this->routes->addCollection($routes);
    }
}
