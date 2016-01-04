<?php

namespace Pagekit\Debug\Event;

use Pagekit\Event\Event;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Event\EventInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author    Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class WrappedListener
{
    protected $listener;
    protected $name;
    protected $priority;
    protected $called;
    protected $stoppedPropagation;
    protected $stopwatch;
    protected $dispatcher;

    public function __construct($listener, $name, $priority, Stopwatch $stopwatch, EventDispatcherInterface $dispatcher = null)
    {
        $this->listener = $listener;
        $this->name = $name;
        $this->priority = $priority;
        $this->stopwatch = $stopwatch;
        $this->dispatcher = $dispatcher;
        $this->called = false;
        $this->stoppedPropagation = false;
    }

    public function getWrappedListener()
    {
        return $this->listener;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function wasCalled()
    {
        return $this->called;
    }

    public function stoppedPropagation()
    {
        return $this->stoppedPropagation;
    }

    public function __invoke(EventInterface $event)
    {
        $this->called = true;

        $e = $this->stopwatch->start($this->name, 'event_listener');

        $args = func_get_args();

        call_user_func_array($this->listener, $args);

        if ($e->isStarted()) {
            $e->stop();
        }

        if ($event->isPropagationStopped()) {
            $this->stoppedPropagation = true;
        }
    }
}
