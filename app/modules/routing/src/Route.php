<?php

namespace Pagekit\Routing;

use Symfony\Component\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * Returns the routes name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the routes name
     *
     * @param string $name
     *
     * @return Route
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }
}
