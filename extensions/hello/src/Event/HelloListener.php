<?php

namespace Pagekit\Hello\Event;

use Pagekit\Framework\ApplicationAware;
use Pagekit\Framework\Event\EventSubscriberInterface;

class HelloListener extends ApplicationAware implements EventSubscriberInterface
{

    public function onBoot($event, $eventName, $dispatcher)
    {
        // do something
    }

    public function anyName() // omit parameters if you do not need them
    {
        // do something
    }

    public static function getSubscribedEvents()
    {
        return array(
             // call onBoot in case of hello.boot event
            'hello.boot' => 'onBoot',

            // use any name and add a priority int if desired
            'hello.boot' => array('anyName', 10)
        );
    }
}