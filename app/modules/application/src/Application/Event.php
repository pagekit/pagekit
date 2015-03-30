<?php

namespace Pagekit\Application;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct($parameters = null)
    {
        $this->parameters = $parameters ?: [];
    }

    /**
     * Determine if the given parameter exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->parameters[$key]);
    }

    /**
     * Gets a parameter value.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : null;
    }

    /**
     * Sets a parameter value.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function offsetSet($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Unsets a parameter value.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->parameters[$key]);
    }
}
