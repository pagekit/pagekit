<?php

namespace Pagekit\Debug\Event;

use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Event\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Psr\Log\LoggerInterface;

/**
 * @author    Fabien Potencier <fabien@symfony.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class TraceableEventDispatcher implements EventDispatcherInterface
{
    protected $logger;
    protected $stopwatch;
    protected $called;
    protected $dispatcher;
    protected $wrappedListeners;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param Stopwatch                $stopwatch
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $dispatcher, Stopwatch $stopwatch, LoggerInterface $logger = null)
    {
        $this->dispatcher = $dispatcher;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
        $this->called = [];
        $this->wrappedListeners = [];
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, $listener, $priority = 0)
    {
        $this->dispatcher->on($event, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function off($event, $listener = null)
    {
        if (isset($this->wrappedListeners[$event])) {
            foreach ($this->wrappedListeners[$event] as $index => $wrappedListener) {
                if ($wrappedListener->getWrappedListener() === $listener) {
                    $listener = $wrappedListener;
                    unset($this->wrappedListeners[$event][$index]);
                    break;
                }
            }
        }

        return $this->dispatcher->off($event, $listener);
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
            $class = $this->dispatcher->getEventClass();
            $e = new $class($event);
        } else {
            $e = $event;
        }

        $event = $e->getName();

        $this->preProcess($event);

        $watch = $this->stopwatch->start($event, 'section');

        $this->dispatcher->trigger($e, $arguments);

        if ($watch->isStarted()) {
            $watch->stop();
        }

        $this->postProcess($event);

        return $e;
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($event = null)
    {
        return $this->dispatcher->hasListeners($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($event = null)
    {
        return $this->dispatcher->getListeners($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($event, $listener)
    {
        return $this->dispatcher->getListenerPriority($event, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventClass()
    {
        return $this->dispatcher->getEventClass();
    }

    /**
     * {@inheritdoc}
     */
    public function getCalledListeners()
    {
        $called = [];
        foreach ($this->called as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $info = $this->getListenerInfo($listener->getWrappedListener(), $eventName);
                $called[$eventName.'.'.$info['pretty']] = $info;
            }
        }

        return $called;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotCalledListeners()
    {
        try {
            $allListeners = $this->getListeners();
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->info('An exception was thrown while getting the uncalled listeners.', ['exception' => $e]);
            }

            // unable to retrieve the uncalled listeners
            return [];
        }

        $notCalled = [];
        foreach ($allListeners as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $called = false;
                if (isset($this->called[$eventName])) {
                    foreach ($this->called[$eventName] as $l) {
                        if ($l->getWrappedListener() === $listener) {
                            $called = true;

                            break;
                        }
                    }
                }

                if (!$called) {
                    $info = $this->getListenerInfo($listener, $eventName);
                    $notCalled[$eventName.'.'.$info['pretty']] = $info;
                }
            }
        }

        uasort($notCalled, [$this, 'sortListenersByPriority']);

        return $notCalled;
    }

    /**
     * Proxies all method calls to the original event dispatcher.
     *
     * @param  string $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->dispatcher, $method], $arguments);
    }

    protected function preProcess($eventName)
    {
        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            $priority = $this->getListenerPriority($eventName, $listener);
            $this->dispatcher->off($eventName, $listener);
            $info = $this->getListenerInfo($listener, $eventName);
            $name = isset($info['class']) ? $info['class'] : $info['type'];
            $wrappedListener = new WrappedListener($listener, $name, $priority, $this->stopwatch, $this);
            $this->wrappedListeners[$eventName][] = $wrappedListener;
            $this->dispatcher->on($eventName, $wrappedListener);
        }
    }

    protected function postProcess($eventName)
    {
        unset($this->wrappedListeners[$eventName]);
        $skipped = false;
        foreach ($this->dispatcher->getListeners($eventName) as $listener) {
            if (!$listener instanceof WrappedListener) {
                continue;
            }
            // Unwrap listener
            $this->dispatcher->off($eventName, $listener);
            $this->dispatcher->on($eventName, $listener->getWrappedListener(), $listener->getPriority());

            $info = $this->getListenerInfo($listener->getWrappedListener(), $eventName);
            if ($listener->wasCalled()) {
                if (null !== $this->logger) {
                    $this->logger->debug(sprintf('Notified event "%s" to listener "%s".', $eventName, $info['pretty']));
                }

                if (!isset($this->called[$eventName])) {
                    $this->called[$eventName] = new \SplObjectStorage();
                }

                $this->called[$eventName]->attach($listener);
            }

            if (null !== $this->logger && $skipped) {
                $this->logger->debug(sprintf('Listener "%s" was not called for event "%s".', $info['pretty'], $eventName));
            }

            if ($listener->stoppedPropagation()) {
                if (null !== $this->logger) {
                    $this->logger->debug(sprintf('Listener "%s" stopped propagation of the event "%s".', $info['pretty'], $eventName));
                }

                $skipped = true;
            }
        }
    }

    /**
     * Returns information about the listener.
     *
     * @param  object $listener
     * @param  string $eventName
     * @return array
     */
    protected function getListenerInfo($listener, $eventName)
    {
        $info = [
            'event' => $eventName,
            'priority' => $this->getListenerPriority($eventName, $listener),
        ];
        if ($listener instanceof \Closure) {

            $refl = new \ReflectionFunction($listener);

            $info += [
                'type' => 'Closure',
                'file' => $refl->getFileName(),
                'line' => $refl->getStartLine(),
                'endline' => $refl->getEndLine(),
                'pretty' => (string) $refl
            ];
        } elseif (is_string($listener)) {
            try {
                $r = new \ReflectionFunction($listener);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }
            $info += [
                'type' => 'Function',
                'function' => $listener,
                'file' => $file,
                'line' => $line,
                'pretty' => $listener,
            ];
        } elseif (is_array($listener) || (is_object($listener) && is_callable($listener))) {
            if (!is_array($listener)) {
                $listener = [$listener, '__invoke'];
            }
            $class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
            try {
                $r = new \ReflectionMethod($class, $listener[1]);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }
            $info += [
                'type' => 'Method',
                'class' => $class,
                'method' => $listener[1],
                'file' => $file,
                'line' => $line,
                'pretty' => $class.'::'.$listener[1],
            ];
        }

        return $info;
    }

    protected function sortListenersByPriority($a, $b)
    {
        if (is_int($a['priority']) && !is_int($b['priority'])) {
            return 1;
        }

        if (!is_int($a['priority']) && is_int($b['priority'])) {
            return -1;
        }

        if ($a['priority'] === $b['priority']) {
            return 0;
        }

        if ($a['priority'] > $b['priority']) {
            return -1;
        }

        return 1;
    }
}
