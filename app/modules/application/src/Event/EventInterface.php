<?php

namespace Pagekit\Event;

interface EventInterface
{
    /**
     * Gets event name.
     *
     * @return string
     */
    public function getName();

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
