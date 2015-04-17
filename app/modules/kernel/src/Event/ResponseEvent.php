<?php

namespace Pagekit\Kernel\Event;

class ResponseEvent extends KernelEvent
{
    /**
     * @var callable
     */
    protected $controller;

    /**
     * Gets the controller.
     *
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Sets the controller.
     *
     * @param  callable $controller
     * @throws \LogicException
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }
}
