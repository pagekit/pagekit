<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use Pagekit\Event\EventDispatcherInterface;

class EventsDataCollector implements DataCollectorInterface
{

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $events = [];
        foreach ($this->dispatcher->getListeners() as $name => $listeners) {
            foreach ($listeners as $listener) {
                $events[] = [
                    'name'     => $name,
                    'listener' => $this->extractListener($listener)
                ];
            }
        }

        return compact('events');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'events';
    }


    /**
     * @param CLosure|object|array $listener
     *
     * @return array|string
     */
    private function extractListener($listener)
    {
        // listener is a closure
        if ($listener instanceof \Closure) {
            $r = $this->reflectClosure($listener);

            return [
                'name'    => get_class($listener),
                'details' => $r ? ($r->getFileName() . ' Line ' . $r->getStartLine()) : ''
            ];
        }

        // listener is a concrete object
        if (is_object($listener)) {
            $c = get_class($listener);

            return [
                'name'    => $this->shorten($c),
                'details' => $c
            ];
        }

        // listener is an array of object and function
        if (is_array($listener) && count($listener) == 2) {
            $c = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];

            return [
                'name'    => $this->shorten($c) . '::' . $listener[1],
                'details' => $c . '::' . $listener[1]
            ];
        }

        return ($listener);
    }

    /**
     * @param string $object
     *
     * @return bool|\ReflectionFunction
     */
    private function reflectClosure($object)
    {
        try {
            return new \ReflectionFunction($object);
        }
        catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private function shorten($className)
    {
        return array_slice(explode('\\', $className), -1)[0];
    }

}
