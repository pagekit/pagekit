<?php

namespace Pagekit\Filter;

/**
 * This filter adds a rel="nofollow" to all HTML anchor elements.
 */
class AddRelNofollow extends AbstractFilter
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
        return preg_replace_callback('|<a (.+?)>|i', function ($matches) {
            $text = $matches[1];
            $text = str_replace([' rel="nofollow"', " rel='nofollow'"], '', $text);
            return "<a $text rel=\"nofollow\">";
        }, (string) $value);
    }
}
