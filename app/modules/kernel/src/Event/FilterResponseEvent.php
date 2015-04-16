<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class FilterResponseEvent extends KernelEvent
{
    /**
     * The current response object.
     *
     * @var Response
     */
    private $response;

    public function __construct($name, HttpKernelInterface $kernel, Request $request, $requestType, Response $response)
    {
        parent::__construct($name, $kernel, $request, $requestType);

        $this->setResponse($response);
    }

    /**
     * Returns the current response object.
     *
     * @return Response
     *
     * @api
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a new response object.
     *
     * @param Response $response
     *
     * @api
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
