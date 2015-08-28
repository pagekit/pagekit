<?php

namespace Pagekit\Filter;

/**
 * This filter keeps only digits of the value.
 */
class DigitsFilter extends AbstractFilter
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
        return str_replace(['-', '+'], '', filter_var((string) $value, FILTER_SANITIZE_NUMBER_INT));
    }
}
