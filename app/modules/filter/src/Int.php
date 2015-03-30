<?php

namespace Pagekit\Filter;

/**
 * This filter converts the value to integer.
 */
class Int extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed  $value
     * 
     * @return integer
     */
    public function filter($value)
    {
        return (int) ((string) $value);
    }
}
