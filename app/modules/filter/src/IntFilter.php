<?php

namespace Pagekit\Filter;

/**
 * This filter converts the value to integer.
 */
class IntFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return (int) ((string) $value);
    }
}
