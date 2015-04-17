<?php

namespace Pagekit\Kernel\Event;

use Pagekit\Event\Event;
use Pagekit\Kernel\HttpKernel;

class KernelEvent extends Event
{
    /**
     * @var mixed
     */
    protected $response;

    /**
     * @var int
     */
    protected $requestType;

    /**
     * Constructor.
     *
     * @param string $name
     * @param int    $requestType
     */
    public function __construct($name, $requestType)
    {
        parent::__construct($name);

        $this->requestType = $requestType;
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
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Gets the request type.
     *
     * @return int
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Checks if this is a master request.
     *
     * @return bool
     */
    public function isMasterRequest()
    {
        return HttpKernel::MASTER_REQUEST === $this->requestType;
    }
}
