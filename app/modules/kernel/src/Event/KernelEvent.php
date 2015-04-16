<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class KernelEvent extends Event
{
    /**
     * The kernel in which this event was thrown.
     *
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * The request the kernel is currently processing.
     *
     * @var Request
     */
    private $request;

    /**
     * The request type the kernel is currently processing.  One of
     * HttpKernelInterface::MASTER_REQUEST and HttpKernelInterface::SUB_REQUEST.
     *
     * @var int
     */
    private $requestType;

    public function __construct($name, HttpKernelInterface $kernel, Request $request, $requestType)
    {
        parent::__construct($name);

        $this->kernel = $kernel;
        $this->request = $request;
        $this->requestType = $requestType;
    }

    /**
     * Returns the kernel in which this event was thrown.
     *
     * @return HttpKernelInterface
     *
     * @api
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Returns the request the kernel is currently processing.
     *
     * @return Request
     *
     * @api
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request type the kernel is currently processing.
     *
     * @return int One of HttpKernelInterface::MASTER_REQUEST and
     *             HttpKernelInterface::SUB_REQUEST
     *
     * @api
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Checks if this is a master request.
     *
     * @return bool True if the request is a master request
     *
     * @api
     */
    public function isMasterRequest()
    {
        return HttpKernelInterface::MASTER_REQUEST === $this->requestType;
    }
}
