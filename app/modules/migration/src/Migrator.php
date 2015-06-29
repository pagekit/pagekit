<?php

namespace Pagekit\Migration;

use Pagekit\Migration\Loader\FilesystemLoader;
use Pagekit\Migration\Loader\LoaderInterface;

class Migrator
{
    /**
     * @var array
     */
    protected $globals = [];

    /**
     * @var string
     */
    protected $pattern = '/^(?<version>.+)\.php$/';

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Gets all global parameters.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Adds a global parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * Gets the migration file pattern.
     *
     * @return array
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Sets the migration file pattern.
     *
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Gets the loader.
     *
     * @return LoaderInterface
     */
    public function getLoader()
    {
        if (!isset($this->loader)) {
            $this->loader = new FilesystemLoader;
        }

        return $this->loader;
    }

    /**
     * Sets the loader.
     *
     * @param  LoaderInterface $loader
     * @return self
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Creates a migration object.
     *
     * @param  string $path
     * @param  string $current
     * @param  array  $parameters
     * @return Migration
     */
    public function create($path, $current = null, $parameters = [])
    {
        return new Migration($this->getLoader()->load($path, $this->pattern, array_replace($this->globals, $parameters)), $current);
    }
}
