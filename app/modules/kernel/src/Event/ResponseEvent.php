<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;

class ResponseEvent extends KernelEvent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor.
     *
     * @param string   $name
     * @param int      $requestType
     * @param Response $response
     */
    public function __construct($name, $requestType, Response $response = null)
    {
        parent::__construct($name, $requestType);

        $this->response = $response;
    }

    /**
     * Checks if a response was set.
     *
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * Gets the response.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }
}
