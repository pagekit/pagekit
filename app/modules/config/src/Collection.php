<?php

namespace Pagekit\Config;

use Pagekit\Util\Arr;

class Collection implements \ArrayAccess, \JsonSerializable
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param mixed $values
     */
    public function __construct($values = [])
    {
        $this->values = (array) $values;
    }

    /**
     * Checks if the given key exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->values, $key);
    }

    /**
     * Gets a value by key.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->values, $key, $default);
    }

    /**
     * Sets a value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function set($key, $value)
    {
        Arr::set($this->values, $key, $value);

        return $this;
    }

    /**
     * Removes one or more values.
     *
     * @param  array|string $keys
     * @return self
     */
    public function remove($keys)
    {
        Arr::remove($this->values, $keys);

        return $this;
    }

    /**
     * Selects a key.
     *
     * @param string $key
     */
    public function select($key = null)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Push value to the end of array.
     *
     * @param  mixed $value
     * @return self
     */
    public function push($value)
    {
        if ($this->key) {
            $values = $this->get($this->key);
        } else {
            $values &= $this->values;
        }

        foreach (func_get_args() as $value) {
            $values[] = $value;
        }

        if ($this->key) {
            $this->set($this->key, $values);
        }

        return $this;
    }

    /**
     * Removes a value from array.
     *
     * @param  mixed $value
     * @return self
     */
    public function pull($value)
    {
        if ($this->key) {
            $values = $this->get($this->key);
        } else {
            $values &= $this->values;
        }

        Arr::pull($values, $value);

        if ($this->key) {
            $this->set($this->key, $values);
        }

        return $this;
    }

    /**
     * Merges a values from another array.
     *
     * @param  mixed $values
     * @param  bool  $replace
     * @return self
     */
    public function merge($values, $replace = false)
    {
        $vals =& $this->key ? $this->get($this->key) : $this->values;
        $vals =& Arr::merge($vals, $values, $replace);

        if ($this->key) {
            $this->set($this->key, $vals);
        }

        return $this;
    }

    /**
     * Gets the values as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * Implements JsonSerializable interface.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->values;
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see has()
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see get()
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see set()
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Implements ArrayAccess interface.
     *
     * @see set()
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
