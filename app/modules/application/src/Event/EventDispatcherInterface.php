<?php

namespace Pagekit\Event;

interface EventDispatcherInterface
{
    /**
     * Adds an event listener.
     *
     * @param string   $event
     * @param callable $listener
     * @param int      $priority
     */
    public function on($event, $listener, $priority = 0);

    /**
     * Removes one or more event listeners.
     *
     * @param string   $event
     * @param callable $listener
     */
    public function off($event, $listener = null);

    /**
     * Adds an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function subscribe(EventSubscriberInterface $subscriber);

    /**
     * Removes an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function unsubscribe(EventSubscriberInterface $subscriber);

    /**
     * Triggers an event.
     *
     * @param  string $event
     * @param  array  $arguments
     * @return EventInterface
     */
    public function trigger($event, array $arguments = []);

    /**
     * Checks if a event has listeners.
     *
     * @param  string $event
     * @return bool
     */
    public function hasListeners($event = null);

    /**
     * Gets all listeners of an event.
     *
     * @param  string $event
     * @return array
     */
    public function getListeners($event = null);
}
