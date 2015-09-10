<?php

namespace Pagekit\Filter;

/**
 * This filter decodes a JSON string to a array.
 */
class JsonFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }
    }
}
