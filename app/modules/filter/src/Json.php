<?php

namespace Pagekit\Filter;

/**
 * This filter decodes a JSON string to a array.
 */
class Json extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed  $value
     *
     * @return array
     */
    public function filter($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }
    }
}
