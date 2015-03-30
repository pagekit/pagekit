<?php

namespace Pagekit\Event;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher
{
    /**
     * {@inheritdoc}
     */
    protected function doDispatch($listeners, $eventName, BaseEvent $event)
    {
        $arguments = [];

        if ($event instanceof Event && $args = $event->getArguments()) {
            $arguments = array_values($args);
        }

        array_unshift($arguments, $event);

        foreach ($listeners as $listener) {
            call_user_func_array($listener, $arguments);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }
}
