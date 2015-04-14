<?php

namespace Pagekit\Profiler\Event;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener as BaseProfilerListener;

class ProfilerListener extends BaseProfilerListener implements EventSubscriberInterface
{
    public function subscribe()
    {
        return static::getSubscribedEvents();
    }
}
