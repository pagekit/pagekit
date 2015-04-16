<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class GetResponseForExceptionEvent extends GetResponseEvent
{
    /**
     * The exception object.
     *
     * @var \Exception
     */
    private $exception;

    public function __construct($name, HttpKernelInterface $kernel, Request $request, $requestType, \Exception $e)
    {
        parent::__construct($name, $kernel, $request, $requestType);

        $this->setException($e);
    }

    /**
     * Returns the thrown exception.
     *
     * @return \Exception The thrown exception
     *
     * @api
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Replaces the thrown exception.
     *
     * This exception will be thrown if no response is set in the event.
     *
     * @param \Exception $exception The thrown exception
     *
     * @api
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
