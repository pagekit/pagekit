<?php

namespace Pagekit\Routing\Controller;

use Symfony\Component\Routing\RouteCollection;

interface ControllerReaderInterface
{
    /**
     * Reads controller routes.
     *
     * @param  string $class
     * @return RouteCollection
     */
    public function read($class);
}
