<?php

namespace Pagekit\Module\Loader;

interface LoaderInterface
{
    /**
     * Loads the module config.
     *
     * @param  string $name
     * @param  array  $config
     * @return array
     */
    public function load($name, array $config);
}
