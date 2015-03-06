<?php

namespace Pagekit\View\Helper;

use Pagekit\Locale\Helper\DateHelper as DateManager;

class DateHelper
{
    /**
     * @var DateManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param DateManager $manager
     */
    public function __construct(DateManager $manager)
    {
        $this->manager = $manager ?: new DateManager;
    }

    /**
     * Formats a date.
     *
     * @return string
     */
    public function __invoke($date, $format = 'medium', $timezone = null)
    {
        return $this->manager->format($date, $format, $timezone);
    }
}
