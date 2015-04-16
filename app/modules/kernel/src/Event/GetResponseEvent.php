<?php

namespace Pagekit\Kernel\Event;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @copyright Copyright (c) 2004-2015 Fabien Potencier
 */
class GetResponseEvent extends KernelEvent
{
    /**
     * The response object.
     *
     * @var Response
     */
    private $response;

    /**
     * Returns the response object.
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
     * Sets a response and stops event propagation.
     *
     * @param Response $response
     *
     * @api
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set.
     *
     * @return bool Whether a response was set
     *
     * @api
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
