<?php

namespace Pagekit\View\Helper;

use Pagekit\Locale\Helper\DateHelper as DateManager;

class DateHelper implements HelperInterface
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
     * @param  string|\DateTime|\DateTimeInterface $date
     * @param  string                              $format
     * @param  \DateTimeZone|string                $timezone
     * @return string
     */
    public function __invoke($date, $format = 'medium', $timezone = null)
    {
        return $this->manager->format($date, $format, $timezone);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}
