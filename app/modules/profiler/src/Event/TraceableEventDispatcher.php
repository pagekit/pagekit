<?php

namespace Pagekit\Profiler\Event;

use Pagekit\Event\Event;
use Pagekit\Event\EventDispatcherInterface;
use Pagekit\Event\EventSubscriberInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\HttpKernel\Profiler\Profiler as BaseProfiler;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Collects some data about event listeners.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableEventDispatcher implements EventDispatcherInterface
{
    protected $logger;
    protected $called;
    protected $stopwatch;
    protected $profiler;
    protected $dispatcher;
    protected $wrappedListeners;
    protected $firstCalledEvent;
    protected $id;
    protected $priorities;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
     * @param Stopwatch                $stopwatch  A Stopwatch instance
     * @param LoggerInterface          $logger     A LoggerInterface instance
     */
    public function __construct(EventDispatcherInterface $dispatcher, Stopwatch $stopwatch, LoggerInterface $logger = null)
    {
        $this->dispatcher = $dispatcher;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
        $this->called = [];
        $this->wrappedListeners = [];
        $this->firstCalledEvent = [];
    }

    /**
     * Sets the profiler.
     *
     * @param BaseProfiler|null $profiler A Profiler instance
     */
    public function setProfiler(BaseProfiler $profiler = null)
    {
        $this->profiler = $profiler;
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, $listener, $priority = 0)
    {
        if (is_array($listener)) {
            $class = get_class($listener[0]);
            $this->priorities[$class.'::'.$listener[1]] = $priority;
        }elseif ($listener instanceof \Closure) {
            $this->priorities[spl_object_hash($listener)] = $priority;
        } elseif (is_object($listener) && is_callable($listener)) {
            $class = get_class($listener);
            $this->priorities[$class.'::__invoke'] = $priority;
        } elseif (is_string($listener)) {
            try {
                $r = new \ReflectionFunction($listener);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }
            $this->priorities[$file.'::'.$line] = $priority;
        }

        $this->dispatcher->on($event, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function off($event, $listener = null)
    {
        return $this->dispatcher->off($event, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventSubscriberInterface $subscriber)
    {
        $class = get_class($subscriber);

        foreach ($subscriber->subscribe() as $params) {
            if (is_string($params)) {
                $this->priorities[$class.'::'.$params] = 0;
            } elseif (is_string($params[0])) {
                $this->priorities[$class.'::'.$params[0]] = isset($params[1]) ? $params[1] : 0;
            } else {
                foreach ($params as $listener) {
                    $this->priorities[$class.'::'.$listener[0]] = isset($listener[1]) ? $listener[1] : 0;
                }
            }
        }

        $this->dispatcher->subscribe($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(EventSubscriberInterface $subscriber)
    {
        return $this->dispatcher->unsubscribe($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($event, array $arguments = [])
    {
        if (is_string($event)) {
            $event = new Event($event);
        }

        $eventName = $event->getName();

        $this->id = spl_object_hash($event);

        $this->preDispatch($eventName, $event, $arguments);

        $e = $this->stopwatch->start($eventName, 'section');

        $this->firstCalledEvent[$eventName] = $this->stopwatch->start($eventName.'.loading', 'event_listener_loading');

        if (!$this->dispatcher->hasListeners($eventName)) {
            $this->firstCalledEvent[$eventName]->stop();
        }

        $this->dispatcher->trigger($event, $arguments);

        // reset the id as another event might have been dispatched during the dispatching of this event
        $this->id = spl_object_hash($event);

        unset($this->firstCalledEvent[$eventName]);

        $e->stop();

        $this->postDispatch($eventName, $event);

        return $event;
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
    public function getCalledListeners()
    {
        return $this->called;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotCalledListeners()
    {
        $notCalled = [];

        foreach ($this->getListeners() as $name => $listeners) {
            foreach ($listeners as $listener) {
                $info = $this->getListenerInfo($listener, $name);
                if (!isset($this->called[$name.'.'.$info['pretty']])) {
                    $notCalled[$name.'.'.$info['pretty']] = $info;
                }
            }
        }

        return $notCalled;
    }

    /**
     * Proxies all method calls to the original event dispatcher.
     *
     * @param string $method    The method name
     * @param array  $arguments The method arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->dispatcher, $method], $arguments);
    }

    /**
     * This is a private method and must not be used.
     *
     * This method is public because it is used in a closure.
     * Whenever Symfony will require PHP 5.4, this could be changed
     * to a proper private method.
     */
    public function logSkippedListeners($eventName, $event, $listener)
    {
        if (null === $this->logger) {
            return;
        }

        $info = $this->getListenerInfo($listener, $eventName);

        $this->logger->debug(sprintf('Listener "%s" stopped propagation of the event "%s".', $info['pretty'], $eventName));

        $skippedListeners = $this->getListeners($eventName);
        $skipped = false;

        foreach ($skippedListeners as $skippedListener) {
            $skippedListener = $this->unwrapListener($skippedListener);

            if ($skipped) {
                $info = $this->getListenerInfo($skippedListener, $eventName);
                $this->logger->debug(sprintf('Listener "%s" was not called for event "%s".', $info['pretty'], $eventName));
            }

            if ($skippedListener === $listener) {
                $skipped = true;
            }
        }
    }

    /**
     * This is a private method.
     *
     * This method is public because it is used in a closure.
     * Whenever Symfony will require PHP 5.4, this could be changed
     * to a proper private method.
     */
    public function preListenerCall($eventName, $listener)
    {
        // is it the first called listener?
        if (isset($this->firstCalledEvent[$eventName])) {
            $this->firstCalledEvent[$eventName]->stop();
            unset($this->firstCalledEvent[$eventName]);
        }

        $info = $this->getListenerInfo($listener, $eventName);

        if (null !== $this->logger) {
            $this->logger->debug(sprintf('Notified event "%s" to listener "%s".', $eventName, $info['pretty']));
        }

        $this->called[$eventName.'.'.$info['pretty']] = $info;

        return $this->stopwatch->start(isset($info['class']) ? $info['class'] : $info['type'], 'event_listener');
    }

    /**
     * Returns information about the listener
     *
     * @param object $listener  The listener
     * @param string $eventName The event name
     *
     * @return array Information about the listener
     */
    protected function getListenerInfo($listener, $eventName)
    {
        $listener = $this->unwrapListener($listener);

        $info = [
            'event' => $eventName,
        ];
        if ($listener instanceof \Closure) {
            try {
                $r = new \ReflectionFunction($listener);
                $file = $r->getFileName();
                $line = $r->getStartLine();
            } catch (\ReflectionException $e) {
                $file = null;
                $line = null;
            }

            //Problem with caught exceptions (listener throws exception -> not unwrapped -> wrong hash in $this->priorities)

            $info += [
                'type' => 'Closure',
                'file'  => $file,
                'line'  => $line,
                'pretty' => spl_object_hash($listener),
                'priority' => isset($this->priorities[spl_object_hash($listener)]) ? $this->priorities[spl_object_hash($listener)] : 'n/a'
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
                'type'  => 'Function',
                'function' => $listener,
                'file'  => $file,
                'line'  => $line,
                'pretty' => $listener,
                'priority' => isset($this->priorities[$file.'::'.$line]) ? $this->priorities[$file.'::'.$line] : 'n/a'
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
                'type'  => 'Method',
                'class' => $class,
                'method' => $listener[1],
                'file'  => $file,
                'line'  => $line,
                'pretty' => $class.'::'.$listener[1],
                'priority' => isset($this->priorities[$class.'::'.$listener[1]]) ? $this->priorities[$class.'::'.$listener[1]] : 'n/a'
            ];
        }

        return $info;
    }

    /**
     * Updates the stopwatch data in the profile hierarchy.
     *
     * @param string  $token          Profile token
     * @param Boolean $updateChildren Whether to update the children altogether
     */
    protected function updateProfiles($token, $updateChildren)
    {
        if (!$this->profiler || !$profile = $this->profiler->loadProfile($token)) {
            return;
        }

        $this->saveInfoInProfile($profile, $updateChildren);
    }

    /**
     * Update the profiles with the timing and events information and saves them.
     *
     * @param Profile $profile        The root profile
     * @param Boolean $updateChildren Whether to update the children altogether
     */
    protected function saveInfoInProfile(Profile $profile, $updateChildren)
    {
        try {
            $collector = $profile->getCollector('memory');
            $collector->updateMemoryUsage();
        } catch (\InvalidArgumentException $e) {
        }

        try {
            $collector = $profile->getCollector('time');
            $collector->setEvents($this->stopwatch->getSectionEvents($profile->getToken()));
        } catch (\InvalidArgumentException $e) {
        }

        try {
            $collector = $profile->getCollector('events');
            $collector->setCalledListeners($this->getCalledListeners());
            $collector->setNotCalledListeners($this->getNotCalledListeners());
        } catch (\InvalidArgumentException $e) {
        }

        $this->profiler->saveProfile($profile);

        if ($updateChildren) {
            foreach ($profile->getChildren() as $child) {
                $this->saveInfoInProfile($child, true);
            }
        }
    }

    protected function preDispatch($eventName, $event, array $arguments)
    {
        // wrap all listeners before they are called
        $this->wrappedListeners[$this->id] = new \SplObjectStorage();

        $listeners = $this->dispatcher->getListeners($eventName);

        foreach ($listeners as $listener) {
            $this->dispatcher->off($eventName, $listener);
            $wrapped = $this->wrapListener($eventName, $listener, $arguments);
            $this->wrappedListeners[$this->id][$wrapped] = $listener;
            $this->dispatcher->on($eventName, $wrapped);
        }

        switch ($eventName) {
            case 'kernel.request':
                $this->stopwatch->openSection();
                break;
            case 'kernel.view':
            case 'kernel.response':
                // stop only if a controller has been executed
                if ($this->stopwatch->isStarted('controller')) {
                    $this->stopwatch->stop('controller');
                }
                break;
            case 'kernel.terminate':
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                // There is a very special case when using builtin AppCache class as kernel wrapper, in the case
                // of an ESI request leading to a `stale` response [B]  inside a `fresh` cached response [A].
                // In this case, `$token` contains the [B] debug token, but the  open `stopwatch` section ID
                // is equal to the [A] debug token. Trying to reopen section with the [B] token throws an exception
                // which must be caught.
                try {
                    $this->stopwatch->openSection($token);
                } catch (\LogicException $e) {}
                break;
        }
    }

    protected function postDispatch($eventName, $event)
    {
        switch ($eventName) {
            case 'kernel.controller':
                $this->stopwatch->start('controller', 'section');
                break;
            case 'kernel.response':
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                $this->stopwatch->stopSection($token);
                if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
                    // The profiles can only be updated once they have been created
                    // that is after the 'kernel.response' event of the main request
                    $this->updateProfiles($token, true);
                }
                break;
            case 'kernel.terminate':
                $token = $event->getResponse()->headers->get('X-Debug-Token');
                // In the special case described in the `preDispatch` method above, the `$token` section
                // does not exist, then closing it throws an exception which must be caught.
                try {
                    $this->stopwatch->stopSection($token);
                } catch (\LogicException $e) {}
                // The children profiles have been updated by the previous 'kernel.response'
                // event. Only the root profile need to be updated with the 'kernel.terminate'
                // timing information.
                $this->updateProfiles($token, false);
                break;
        }

        if (isset($this->wrappedListeners[$this->id])) {
            foreach ($this->wrappedListeners[$this->id] as $wrapped) {
                $this->dispatcher->off($eventName, $wrapped);
                $this->dispatcher->on($eventName, $this->wrappedListeners[$this->id][$wrapped]);
            }
        }

        unset($this->wrappedListeners[$this->id]);
    }

    protected function wrapListener($eventName, $listener, array $arguments)
    {
        return function ($event) use ($eventName, $listener, $arguments) {
            $e = $this->preListenerCall($eventName, $listener);

            array_unshift($arguments, $event);
            call_user_func_array($listener, $arguments);

            $e->stop();

            if ($event->isPropagationStopped()) {
                $this->logSkippedListeners($eventName, $event, $listener);
            }
        };
    }

    protected function unwrapListener($listener)
    {
        // get the original listener
        if (is_object($listener) && isset($this->wrappedListeners[$this->id][$listener])) {
            return $this->wrappedListeners[$this->id][$listener];
        }

        return $listener;
    }
}
