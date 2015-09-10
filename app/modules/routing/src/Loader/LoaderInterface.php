<?php

namespace Pagekit\Routing\Loader;

use Pagekit\Routing\Route;

interface LoaderInterface
{
    /**
     * Loads routes.
     *
     * @param  mixed $routes
     * @return Route[]
     */
    public function load($routes);
}
