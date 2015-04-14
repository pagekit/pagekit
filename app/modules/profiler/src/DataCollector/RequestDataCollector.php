<?php

namespace Pagekit\Profiler\DataCollector;

use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector as DataCollector;

class RequestDataCollector extends DataCollector implements EventSubscriberInterface
{
    public function subscribe()
    {
        return static::getSubscribedEvents();
    }
}
