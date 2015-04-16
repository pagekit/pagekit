<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class PostResponseEvent extends Event
{
    /**
     * The kernel in which this event was thrown.
     *
     * @var HttpKernelInterface
     */
    private $kernel;

    private $request;

    private $response;

    public function __construct(HttpKernelInterface $kernel, Request $request, Response $response)
    {
        $this->name = 'kernel.terminate';
        $this->kernel = $kernel;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Returns the kernel in which this event was thrown.
     *
     * @return HttpKernelInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * Returns the request for which this event was thrown.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the response for which this event was thrown.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
