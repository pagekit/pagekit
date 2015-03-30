<?php

namespace Pagekit\Config\Loader;

interface LoaderInterface
{
    /**
     * Load the given configuration file.
     *
     * @param string $filename
     * 
     * @return array
     */
    public function load($filename);
 
    /**
     * Determine if the configuration file is supported.
     *
     * @param string $filename
     * 
     * @return bool
     */
    public function supports($filename);
}
