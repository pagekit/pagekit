<?php

namespace Pagekit\View\Asset;

class ExportAsset extends StringAsset
{
    /**
     * @var array
     */
    protected $asset = [];

    /**
     * Gets a value.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->asset[$key]) ? $this->asset[$key] : $default;
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
        $this->asset[$key] = $value;

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
        $this->asset = array_replace_recursive($this->asset, $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('var %s = %s;', $this->name, json_encode($this->asset));
    }
}
