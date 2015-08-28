<?php

namespace Pagekit\Filter;

/**
 * This filter strips the newline control characters of the value.
 */
class StripNewlinesFilter extends AbstractFilter
{
    /**
     * Returns the filtered value.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        return str_replace(["\n", "\r"], '', (string) $value);
    }
}
