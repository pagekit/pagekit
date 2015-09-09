<?php

namespace Pagekit\Filter;

/**
 * This filter converts the value unicode slug.
 */
class SlugifyFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $value = preg_replace('/\xE3\x80\x80/', ' ', (string) $value);
        $value = str_replace('-', ' ', $value);
        $value = preg_replace('#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $value);
        $value = str_replace('?', '', $value);
        $value = trim(mb_strtolower($value, 'UTF-8'));
        $value = preg_replace('#\x20+#', '-', $value);

        return $value;
    }
}
