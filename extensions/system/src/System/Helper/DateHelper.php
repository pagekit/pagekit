<?php

namespace Pagekit\System\Helper;

use DateInterval;
use DateTime;
use DateTimeZone;

class DateHelper
{
    /* date/time format types */
    const NONE     = -1;
    const FULL     = 0;
    const LONG     = 1;
    const MEDIUM   = 2;
    const SHORT    = 3;
    const INTERVAL = 4;

    /**
     * @var DateTimeZone
     */
    protected $timezone;

    /**
     * @var array
     */
    protected $formats = [
        self::NONE      => '',
        self::FULL      => 'l, F d, y',
        self::LONG      => 'F d, y',
        self::MEDIUM    => 'M d, Y',
        self::SHORT     => 'n/d/y',
        self::INTERVAL  => '%d days'
    ];

    /**
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        if (!$this->timezone) {
            $this->timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $this->timezone;
    }

    /**
     * @param DateTimeZone|string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param array $formats
     */
    public function setFormats(array $formats)
    {
        $this->formats = $formats;
    }

    /**
     * @param  string|DateTime|\DateTimeInterface $date
     * @param  int|string                         $format
     * @param  DateTimeZone|string                $timezone
     * @param  bool                               $translate
     * @return mixed|string
     */
    public function format($date, $format = self::MEDIUM, $timezone = null, $translate = true)
    {
        if (isset($this->formats[$format])) {
            $format = $this->formats[$format];
        }

        if (!$date instanceof DateInterval) {
            $date = $this->getDateTime($date, $timezone);

            if ($translate) {
                return $this->translate($date, $format);
            }
        }

        return $date->format($format);
    }

    /**
     * @param  string|DateTime|\DateTimeInterface $date
     * @param  DateTimeZone|string                $timezone
     * @return DateTime|null|string
     */
    public function getDateTime($date = null, $timezone = null)
    {
        if (!$timezone) {
            $defaultTimezone = $this->getTimezone();
        } elseif (!$timezone instanceof DateTimeZone) {
            $defaultTimezone = new DateTimeZone($timezone);
        } else {
            $defaultTimezone = $timezone;
        }

        if ($date instanceof DateTime || $date instanceof \DateTimeInterface) {

            $returningDate = new DateTime($date->format('c'));

            return $returningDate->setTimezone(false !== $timezone ? $defaultTimezone : $date->getTimezone());
        }

        $asString = (string) $date;

        if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
            $date = '@'.$date;
        }

        $date = new DateTime($date, $defaultTimezone);

        if (false !== $timezone) {
            $date->setTimezone($defaultTimezone);
        }

        return $date;
    }

    /**
     * @param  DateTime $date
     * @param  $format
     * @return mixed
     */
    protected function translate(DateTime $date, $format)
    {
        // Encode markers that should be translated. 'A' becomes
        // '\xEF\AA\xFF'. xEF and xFF are invalid UTF-8 sequences,
        // and we assume they are not in the input string.
        // Paired backslashes are isolated to prevent errors in
        // read-ahead evaluation. The read-ahead expression ensures that
        // A matches, but not \A.
        $format = preg_replace(['/\\\\\\\\/', '/(?<!\\\\)([AaeDlMTF])/'], ["\xEF\\\\\\\\\xFF", "\xEF\\\\\$1\$1\xFF"], $format);

        $format = $date->format($format);

        // Translate the marked sequences.
        return preg_replace_callback('/\xEF[AaeDlMTF]?(.*?)\xFF/', function(array $matches = null) { return __($matches[1]); }, $format);
    }
}
