<?php

namespace Pagekit\Application\Console\Event;

use Pagekit\Application\Console\Application;
use Symfony\Component\EventDispatcher\Event;

class ConsoleEvent extends Event
{
    /**
     * @var Application
     */
    protected $console;

    /**
     * Constructor.
     *
     * @param Application $console
     */
    public function __construct(Application $console)
    {
        $this->console = $console;
    }

    /**
     * Returns the console application.
     *
     * @return Application
     */
    public function getConsole()
    {
        return $this->console;
    }
}
