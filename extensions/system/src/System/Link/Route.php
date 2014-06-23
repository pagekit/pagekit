<?php

namespace Pagekit\System\Link;

use Pagekit\Framework\ApplicationTrait;

abstract class Route implements LinkInterface
{
    use ApplicationTrait;

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

    /**
     * {@inheritdoc}
     */
    public function renderForm($link, $params = [])
    {
        return $this('view')->render('system/admin/links/route.razr', compact('link'));
    }
}
