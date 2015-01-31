<?php

namespace Pagekit\Migration\Loader;

interface LoaderInterface
{
    /**
     * Loads migrations.
     *
     * @param  string $path
     * @param  string $pattern
     * @param  array  $parameters
     * @return array[]
     */
    public function load($path, $pattern, $parameters = array());
}
