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
     * Handles the request.
     *
     * @param  Request $request
     * @return Response
     */
    public function handle(Request $request);

    /**
     * Aborts the current request with HTTP exception.
     *
     * @param  int    $code
     * @param  string $message
     * @param  array  $headers
     * @throws HttpException
     */
    public function abort($code, $message = null, array $headers = []);

    /**
     * Terminates the current request.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response);
}
