<?php

namespace Pagekit\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $arguments;

    /**
     * Constructor.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    /**
     * Get argument by key.
     *
     * @param  string $key
     * @return mixed
     */
    public function getArgument($key)
    {
        if ($this->hasArgument($key)) {
            return $this->arguments[$key];
        }

        throw new \InvalidArgumentException(sprintf('%s not found in %s', $key, $this->getName()));
    }

    /**
     * Add argument to event.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;

        return $this;
    }

    /**
     * Getter for all arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set args property.
     *
     * @param  array $args
     * @return self
     */
    public function setArguments(array $args = [])
    {
        $this->arguments = $args;

        return $this;
    }

    /**
     * Has argument.
     *
     * @param  string $key
     * @return bool
     */
    public function hasArgument($key)
    {
        return array_key_exists($key, $this->arguments);
    }

    /**
     * ArrayAccess for argument getter.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->getArgument($key);
    }

    /**
     * ArrayAccess for argument setter.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function offsetSet($key, $value)
    {
        $this->setArgument($key, $value);
    }

    /**
     * ArrayAccess for unset argument.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        if ($this->hasArgument($key)) {
            unset($this->arguments[$key]);
        }
    }

    /**
     * ArrayAccess has argument.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->hasArgument($key);
    }

    /**
     * IteratorAggregate for iterating over the object like an array.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->arguments);
    }
}
