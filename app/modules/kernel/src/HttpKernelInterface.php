<?php

namespace Pagekit\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface HttpKernelInterface
{
    /**
     * Gets the current request.
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Checks if this is a master request.
     *
     * @return bool
     */
    public function isMasterRequest();

    /**
     * Handles a Request.
     *
     * @param  Request $request
     * @return Response
     */
    public function handle(Request $request);

    /**
     * Terminates a Request.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response);
}
