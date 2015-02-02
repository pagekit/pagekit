<?php

namespace Pagekit\System\Link;

abstract class Route implements LinkInterface
{
    /**
     * Returns the route for this link type
     */
    abstract protected function getRoute();

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getRoute();
    }

    /**
     * {@inheritdoc}
     */
    public function accept($route)
    {
        return $route == $this->getRoute();
    }
}
