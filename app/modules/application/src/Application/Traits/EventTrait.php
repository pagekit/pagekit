<?php

namespace Pagekit\Application\Traits;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;

trait EventTrait
{
    /**
     * @see EventDispatcher::on()
     */
    public static function on($event, $callback, $priority = 0)
    {
        App::events()->on($event, $callback, $priority);
    }

    /**
     * @see EventDispatcher::subscribe()
     */
    public static function subscribe(EventSubscriberInterface $subscriber)
    {
        $subscribers = func_num_args() > 1 ? func_get_args() : [$subscriber];

        foreach ($subscribers as $sub) {
            App::events()->subscribe($sub);
        }
    }

    /**
     * @see EventDispatcher::trigger()
     */
    public static function trigger($event, array $arguments = [])
    {
        return App::events()->trigger($event, $arguments);
    }
}
