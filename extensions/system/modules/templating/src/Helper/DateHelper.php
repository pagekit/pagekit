<?php

namespace Pagekit\Templating\Helper;

use Pagekit\Locale\Helper\DateHelper as DateManager;
use Symfony\Component\Templating\Helper\Helper;

class DateHelper extends Helper
{
    /**
     * @var DateManager
     */
    protected $manager;

    public function __construct(DateManager $manager)
    {
        $this->manager = $manager ?: new DateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }

    public function format($date, $format = 'medium', $timezone = null)
    {
        return $this->manager->format($date, $format, $timezone);
    }
}
