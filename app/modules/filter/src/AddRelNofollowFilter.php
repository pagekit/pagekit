<?php

namespace Pagekit\Filter;

/**
 * This filter adds a rel="nofollow" to all HTML anchor elements.
 */
class AddRelNofollowFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
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
