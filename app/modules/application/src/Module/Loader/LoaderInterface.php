<?php

namespace Pagekit\Module\Loader;

interface LoaderInterface
{
    /**
     * Loads the module.
     *
     * @param  string $name
     * @param  array  $module
     * @return array
     */
    public function load($name, array $module);
}
