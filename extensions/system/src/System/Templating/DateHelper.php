<?php

namespace Pagekit\System\Templating;

use Pagekit\System\Helper\DateHelper as DateManager;
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
        $formats = [
            'none'     => DateManager::NONE,
            'short'    => DateManager::SHORT,
            'medium'   => DateManager::MEDIUM,
            'long'     => DateManager::LONG,
            'full'     => DateManager::FULL,
            'interval' => DateManager::INTERVAL
        ];

        return $this->manager->format($date, isset($formats[$format]) ? $formats[$format] : $format, $timezone, $format);
    }
} 