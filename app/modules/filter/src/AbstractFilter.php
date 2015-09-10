<?php

namespace Pagekit\Filter;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * Filter options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Returns the filter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the filter options.
     *
     * @param  array $options
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (method_exists($this, $method = 'set'.$key)) {
                $this->$method($value);
            } elseif (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                throw new \InvalidArgumentException(sprintf("Unknown option '%s' on filter '%s'.", $key, get_class($this)));
            }
        }

        return $this;
    }
}
