<?php

namespace Pagekit\Filter;

/**
 * This filter converts the value to string.
 */
class String extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        return (string) $value;
    }
}
