<?php

namespace Pagekit\Auth\Event;

use Symfony\Component\HttpFoundation\Response;

class GetResponseEvent extends Event
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * Returns the response object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set
     *
     * @return Boolean Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
