<?php

namespace Pagekit\Filter;

/**
 * This filter keeps only alphabetic characters of the value.
 */
class AlphaFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return preg_replace('/[^[:alpha:]]/u', '', (string) $value);
    }
}
