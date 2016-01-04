<?php

namespace Pagekit\Event;

class PrefixEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var EventDispatcherInterface
     */
    protected $events;

    /**
     * Constructor.
     *
     * @param  string                   $prefix
     * @param  EventDispatcherInterface $events
     */
    public function __construct($prefix, EventDispatcherInterface $events = null)
    {
        $this->prefix = $prefix;
        $this->events = $events ?: new EventDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, $listener, $priority = 0)
    {
        $this->events->on($this->prefix.$event, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function off($event, $listener = null)
    {
        $this->events->off($this->prefix.$event, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventSubscriberInterface $subscriber)
    {
        $this->events->subscribe($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(EventSubscriberInterface $subscriber)
    {
        $this->events->unsubscribe($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($event, array $arguments = [])
    {
        if (is_string($event)) {
            $event = $this->prefix.$event;
        } else if ($event instanceof EventInterface) {
            $event->setName($this->prefix.$event->getName());
        }

        return $this->events->trigger($event, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($event = null)
    {
        return $this->events->hasListeners($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($event = null)
    {
        return $this->events->getListeners($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($event, $listener)
    {
        return $this->events->getListenerPriority($event, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventClass()
    {
        return $this->events->getEventClass();
    }
}
