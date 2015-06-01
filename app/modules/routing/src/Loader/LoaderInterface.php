<?php

namespace Pagekit\Routing\Loader;

interface LoaderInterface
{
    /**
     * Loads a route collection.
     *
     * @param  mixed $routes
     * @return RouteCollection
     */
    public function load($routes);
}
