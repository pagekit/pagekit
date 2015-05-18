<?php

namespace Pagekit\Intl\Helper;

use Pagekit\Intl\Intl;

class DateHelper
{
    /**
     * Format shortcut.
     *
     * @see format()
     */
    public function __invoke($value, $width = 'medium', $locale = '')
    {
        return $this->format($value, $width, $locale);
    }

    /**
     * Formats a date.
     *
     * @param  mixed  $value
     * @param  string $width
     * @param  string $locale
     * @return string
     */
    public function format($value, $width = 'medium', $locale = '')
    {
        $calendar = Intl::calendar();

        if (!$value instanceof \DateTime) {
            $value = $calendar->toDateTime($value);
        }

        if ($width === 'iso') {
            $width = 'yyyy-MM-ddTHH:mm:ssZZZZ';
        }

        if (in_array($width, ['full', 'long', 'medium', 'short'])) {
            return $calendar->formatDate($value, $width, $locale);
        }

        return $calendar->format($value, $width, $locale);
    }

    /**
     * Converts a date/time representation.
     *
     * @param  mixed $value
     * @return \DateTime
     */
    public function parse($value)
    {
        return Intl::calendar()->toDateTime($value);
    }
}
