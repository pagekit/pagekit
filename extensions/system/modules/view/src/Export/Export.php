<?php

namespace Pagekit\View\Export;

class Export implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * Gets a value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
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
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Adds the given values.
     *
     * @param  array $values
     * @return self
     */
    public function add(array $values)
    {
        $this->values = array_replace_recursive($this->values, $values);

        return $this;
    }

    /**
     * Gets values for JSON serialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->values;
    }
}
