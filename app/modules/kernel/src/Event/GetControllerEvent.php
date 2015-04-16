<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Symfony\Component\HttpFoundation\Request;

class GetControllerEvent extends Event
{
    protected $request;
    protected $controller;

    /**
     * Constructs an event.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->name = 'controller.resolve';
        $this->request = $request;
    }

    /**
     * Gets request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return callable|null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param callable $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        $this->stopPropagation();
    }
}
