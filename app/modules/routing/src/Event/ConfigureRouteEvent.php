<?php

namespace Pagekit\Routing\Event;

use Pagekit\Event\Event;
use Symfony\Component\Routing\Route;

class ConfigureRouteEvent extends Event
{
    /**
     * @var Route
     */
    protected $route;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->name  = 'route.configure';
        $this->route = $route;
    }

    /**
     * Gets the route.
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the route.
     *
     * @param Route
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Gets the controller.
     *
     * @return mixed
     */
    public function getController()
    {
        $controller = $this->route->getDefault('_controller');

        if (is_string($controller)) {
            return explode('::', $controller, 2);
        }

        return $controller;
    }

    /**
     * Gets the controller reflection class.
     *
     * @return \ReflectionClass
     */
    public function getControllerClass()
    {
        $controller = $this->getController();

        if (is_array($controller)) {
            return new \ReflectionClass($controller[0]);
        }
    }

    /**
     * Gets the controller reflection method.
     *
     * @return \ReflectionMethod
     */
    public function getControllerMethod()
    {
        $controller = $this->getController();

        if (is_array($controller)) {
            return new \ReflectionMethod($controller[0], $controller[1]);
        }
    }
}
