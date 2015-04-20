<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /**
     * @var Response
     */
    protected $response;

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
