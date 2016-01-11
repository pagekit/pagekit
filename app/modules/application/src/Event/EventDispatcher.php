<?php

namespace Pagekit\Event;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var string
     */
    protected $event;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $sorted = [];

    /**
     * Constructor.
     *
     * @param string $event
     */
    public function __construct($event = 'Pagekit\Event\Event')
    {
        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, $listener, $priority = 0)
    {
        $this->listeners[$event][$priority][] = $listener;
        unset($this->sorted[$event]);
    }

    /**
     * {@inheritdoc}
     */
    public function off($event, $listener = null)
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        if ($listener === null) {
            unset($this->listeners[$event], $this->sorted[$event]);
            return;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$event][$priority][$key], $this->sorted[$event]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->subscribe() as $event => $params) {

            if (is_string($params)) {
                $this->on($event, [$subscriber, $params]);
            } elseif (is_callable($params)) {
                $this->on($event, $params->bindTo($subscriber, $subscriber));
            } elseif (is_string($params[0])) {
                $this->on($event, [$subscriber, $params[0]], isset($params[1]) ? $params[1] : 0);
            } elseif (is_callable($params[0])) {
                $this->on($event, $params[0]->bindTo($subscriber, $subscriber), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    if (is_string($listener[0])) {
                        $this->on($event, [$subscriber, $listener[0]], isset($listener[1]) ? $listener[1] : 0);
                    } else {
                        $this->on($event, $listener[0]->bindTo($subscriber, $subscriber), isset($listener[1]) ? $listener[1] : 0);
                    }
                }
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->subscribe() as $event => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->off($event, [$subscriber, $listener[0]]);
                }
            } else {
                $this->off($event, [$subscriber, is_string($params) ? $params : $params[0]]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($event, array $arguments = [])
    {
        if (is_string($event)) {
            $e = new $this->event($event);
        } else {
            $e = $event;
        }

        $e->setDispatcher($this);

        array_unshift($arguments, $e);

        foreach ($this->getListeners($e->getName()) as $listener) {

            call_user_func_array($listener, $arguments);

            if ($e->isPropagationStopped()) {
                break;
            }
        }

        return $e;
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($event = null)
    {
        return (bool) count($this->getListeners($event));
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($event = null)
    {
        if ($event !== null) {
            return isset($this->sorted[$event]) ? $this->sorted[$event] : $this->sortListeners($event);
        }

        foreach (array_keys($this->listeners) as $event) {
            if (!isset($this->sorted[$event])) {
                $this->sortListeners($event);
            }
        }

        return array_filter($this->sorted);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($event, $listener)
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== array_search($listener, $listeners, true)) {
                return $priority;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEventClass()
    {
        return $this->event;
    }

    /**
     * Sorts all listeners of an event by their priority.
     *
     * @param  string $event
     * @return array
     */
    protected function sortListeners($event)
    {
        $sorted = [];

        if (isset($this->listeners[$event])) {
            krsort($this->listeners[$event]);
            $sorted = call_user_func_array('array_merge', $this->listeners[$event]);
        }

        return $this->sorted[$event] = $sorted;
    }
}
