<?php

namespace Pagekit\Application\Traits;

use Pagekit\Event\EventSubscriberInterface;

trait EventTrait
{
    /**
     * @see EventDispatcher::on()
     */
    public static function on($event, $callback, $priority = 0)
    {
        if (static::$instance->booted) {
            static::events()->on($event, $callback, $priority);
            return;
        }

        static::$instance->extend('events', function ($dispatcher) use ($event, $callback, $priority) {
            $dispatcher->on($event, $callback, $priority);
            return $dispatcher;
        });

    }

    /**
     * @see EventDispatcher::subscribe()
     */
    public static function subscribe(EventSubscriberInterface $subscriber)
    {
        $subscribers = func_num_args() > 1 ? func_get_args() : [$subscriber];

        if (static::$instance->booted) {
            foreach ($subscribers as $sub) {
                static::events()->subscribe($sub);
            }
            return;
        }

        try {
            static::$instance->extend('events', function ($dispatcher) use ($subscribers) {
                foreach ($subscribers as $sub) {
                    $dispatcher->subscribe($sub);
                }
                return $dispatcher;
            });
        } catch (\Exception $e) {
            $test = $e;
        }
    }

    /**
     * @see EventDispatcher::trigger()
     */
    public static function trigger($event, array $arguments = [])
    {
        return static::events()->trigger($event, $arguments);
    }
}
