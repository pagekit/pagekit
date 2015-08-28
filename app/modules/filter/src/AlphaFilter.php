<?php

namespace Pagekit\Filter;

/**
 * This filter keeps only alphabetic characters of the value.
 */
class AlphaFilter extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed  $value
     *
     * @return string
     */
    public function filter($value)
    {
        return preg_replace('/[^[:alpha:]]/u', '', (string) $value);
    }
}
