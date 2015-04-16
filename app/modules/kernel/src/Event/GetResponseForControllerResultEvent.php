<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class GetResponseForControllerResultEvent extends GetResponseEvent
{
    /**
     * The return value of the controller.
     *
     * @var mixed
     */
    private $controllerResult;

    public function __construct($name, HttpKernelInterface $kernel, Request $request, $requestType, $controllerResult)
    {
        parent::__construct($name, $kernel, $request, $requestType);

        $this->controllerResult = $controllerResult;
    }

    /**
     * Returns the return value of the controller.
     *
     * @return mixed The controller return value
     *
     * @api
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }

    /**
     * Assigns the return value of the controller.
     *
     * @param mixed $controllerResult The controller return value
     *
     * @api
     */
    public function setControllerResult($controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
}
