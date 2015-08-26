<?php

namespace Pagekit\Filter;

/**
 * This filter generates the slug of the given value.
 */
class Slug extends AbstractFilter
{
    /**
     * @inheritdoc
     */
    public function filter($value)
    {
        // replace ideographic space with space
        $value = preg_replace('/\xE3\x80\x80/', ' ', $value);
        // replace dash with space
        $value = str_replace('-', ' ', $value);
        // replace special characters with space
        $value = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $value);
        // remove interrogation mark
        $value = str_replace('?', '', $value);
        // make lowercase, remove spaces at the beginning and end
        $value = trim(mb_strtolower($value, 'UTF-8'));
        // replace every single or series of spaces with a single dash
        $value = preg_replace('#\x20+#', '-', $value);

        return $value;
    }
}
