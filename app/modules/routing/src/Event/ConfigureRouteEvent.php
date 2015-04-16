<?php

namespace Pagekit\Routing\Event;

use ReflectionClass;
use ReflectionMethod;
use Pagekit\Event\Event;
use Symfony\Component\Routing\Route;

class ConfigureRouteEvent extends Event
{
    protected $route;
    protected $class;
    protected $method;
    protected $options;

    /**
     * Constructs an event.
     *
     * @param Route            $route
     * @param ReflectionClass  $class
     * @param ReflectionMethod $method
     * @param array            $options
     */
    public function __construct(Route $route, ReflectionClass $class, ReflectionMethod $method, array $options)
    {
        $this->name    = 'route.configure';
        $this->route   = $route;
        $this->class   = $class;
        $this->method  = $method;
        $this->options = $options;
    }

    /**
     * Returns the route for this event.
     *
     * @return Route|null
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute(Route $route = null)
    {
        $this->route = $route;
    }

    /**
     * Returns the reflection class for this event.
     *
     * @return ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Returns the reflection method for this event.
     *
     * @return ReflectionMethod
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns the options for this event.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
