<?php

namespace Pagekit\Module\Loader;

interface LoaderInterface
{
    /**
     * Loads the module.
     *
     * @param  string $name
     * @param  mixed  $module
     * @return mixed
     */
    public function load($name, $module);
}
