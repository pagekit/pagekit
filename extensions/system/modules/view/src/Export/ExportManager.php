<?php

namespace Pagekit\View\Export;

class ExportManager implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $exports = [];

    /**
     * Get shortcut.
     *
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * Gets a export.
     *
     * @param  string $name
     * @return Export
     */
    public function get($name)
    {
        if (!isset($this->exports[$name])) {
            $this->set($name, new Export);
        }

        return $this->exports[$name];
    }

    /**
     * Sets a export.
     *
     * @param string $name
     * @param Export $export
     */
    public function set($name, Export $export)
    {
        $this->exports[$name] = $export;
    }

    /**
     * Checks if a export exists.
     *
     * @param  string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->exports[$name]);
    }

    /**
     * Gets a export.
     *
     * @see get()
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Sets a export.
     *
     * @see set()
     */
    public function offsetSet($name, $export)
    {
        $this->set($name, $export);
    }

    /**
     * Unsets a export.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->exports[$name]);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->exports);
    }
}
