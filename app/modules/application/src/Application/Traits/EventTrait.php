<?php

namespace Pagekit\Application\Traits;

use Pagekit\Application as App;
use Pagekit\Event\Event as GenericEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

trait EventTrait
{
    /**
     * Dispatches an event to all registered listeners.
     *
     * @param  string      $eventName
     * @param  Event|array $event
     * @return Event
     */
    public static function trigger($eventName, $event = null)
    {
        if (is_array($event)) {
            $event = new GenericEvent($event);
            $event->setName($eventName);
        }

        return App::events()->dispatch($eventName, $event);
    }

    /**
     * @see EventDispatcherInterface::addListener
     */
    public static function on($event, $callback, $priority = 0)
    {
        App::events()->addListener($event, $callback, $priority);
    }

    /**
     * @see EventDispatcherInterface::addSubscriber
     */
    public static function subscribe(EventSubscriberInterface $subscriber)
    {
        $subscribers = func_num_args() > 1 ? func_get_args() : [$subscriber];

        foreach ($subscribers as $sub) {
            App::events()->addSubscriber($sub);
        }
    }
}
