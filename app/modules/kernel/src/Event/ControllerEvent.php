<?php

namespace Pagekit\Kernel\Event;

class ControllerEvent extends KernelEvent
{
    use ResponseTrait;

    /**
     * @var callable
     */
    protected $controller;

    /**
     * @var mixed
     */
    protected $controllerResult;

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
     * @param callable $controller
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Gets the controller result.
     *
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * Sets the controller result.
     *
     * @param mixed $controllerResult
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
}
