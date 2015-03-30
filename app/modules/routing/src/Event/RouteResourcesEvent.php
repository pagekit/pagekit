<?php

namespace Pagekit\Routing\Event;

use Pagekit\Routing\Router;
use Symfony\Component\EventDispatcher\Event;

class RouteResourcesEvent extends Event
{
    protected $resources;

    /**
     * Constructs an event.
     */
    public function __construct()
    {
        $this->resources = [];
    }

    /**
     * Gets route resources.
     *
     * @return Router
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Adds route resources.
     *
     * @param array $resources
     */
    public function addResources(array $resources = [])
    {
        $this->resources = array_merge($this->resources, $resources);
    }
}
