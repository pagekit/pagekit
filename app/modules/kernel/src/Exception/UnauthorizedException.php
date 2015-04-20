<?php

namespace Pagekit\Kernel\Exception;

class UnauthorizedException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 401, $previous = null)
    {
        parent::__construct($message ?: 'Unauthorized', $code, $previous);
    }
}
