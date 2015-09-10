<?php

namespace Pagekit\Config;

use Pagekit\Util\Arr;

class Config implements \ArrayAccess, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var bool
     */
    protected $dirty = false;

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

        $this->dirty = true;

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

        $this->dirty = true;

        return $this;
    }

    /**
     * Push value to the end of array.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function push($key, $value)
    {
        $values = $this->get($key);

        $values[] = $value;

        return $this->set($key, $values);
    }

    /**
     * Removes a value from array.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self
     */
    public function pull($key, $value)
    {
        $values = $this->get($key);

        Arr::pull($values, $value);

        return $this->set($key, $values);
    }

    /**
     * Merges values from another array.
     *
     * @param  mixed $values
     * @param  bool  $replace
     * @return self
     */
    public function merge($values, $replace = false)
    {
        $this->values = Arr::merge($this->values, $values, $replace);
        $this->dirty  = true;

        return $this;
    }

    /**
     * Extracts config values.
     *
     * @param  array $keys
     * @param  bool  $include
     * @return array
     */
    public function extract($keys, $include = true)
    {
        return Arr::extract($this->values, $keys, $include);
    }

    /**
     * Checks if the values are modified.
     *
     * @return array
     */
    public function dirty()
    {
        return $this->dirty;
    }

    /**
     * Dumps the values as php.
     *
     * @return string
     */
    public function dump()
    {
        return '<?php return '.var_export($this->values, true).';';
    }

    /**
     * Gets the value count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
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
     * @see remove()
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }
}
