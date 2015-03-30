<?php

namespace Pagekit\Filter;

/**
 * This filter keeps only alphabetic characters and digits of the value.
 */
class Alnum extends AbstractFilter
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
        return preg_replace('/[^[:alnum:]]/u', '', (string) $value);
    }
}
