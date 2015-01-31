<?php

namespace Pagekit\Package\Loader;

interface LoaderInterface
{
    /**
     * Creates a package instance based on a given package config.
     *
     * @param  mixed   $config
     * @param  string  $class
     * @return \Pagekit\Package\PackageInterface
     */
    public function load($config, $class = 'Pagekit\Package\Package');
}
