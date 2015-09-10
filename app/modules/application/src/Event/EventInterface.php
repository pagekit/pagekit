<?php

namespace Pagekit\Event;

interface EventInterface
{
    /**
     * Gets the event name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher();

    /**
     * Is propagation stopped?
     *
     * @return bool
     */
    public function isPropagationStopped();

    /**
     * Stop further event propagation.
     *
     * @return void
     */
    public function stopPropagation();
}
