<?php

namespace Pagekit\Filter;

/**
 * This filter converts the value to boolean.
 */
class BooleanFilter extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed $value
     * @return bool
     */
    public function filter($value)
    {
        return (bool) @strval($value);
    }
}
