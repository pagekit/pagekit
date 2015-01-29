<?php

namespace Pagekit\System\Package;

abstract class PackageManager implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $loaded = [];

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Get shortcut.
     *
     * @see get()
     * @return mixed|null
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets an instance by name.
     *
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->loaded[$name]) ? $this->loaded[$name] : null;
    }

    /**
     * Implements the \IteratorAggregate.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->loaded);
    }

    /**
     * Loads an package bootstrap file.
     *
     * @param  string $name
     * @param  string $path
     * @return mixed
     */
    abstract public function load($name, $path = null);
}
