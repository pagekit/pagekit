<?php

namespace Pagekit\Kernel\Exception;

class ForbiddenException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 403, $previous = null)
    {
        parent::__construct($message ?: 'Forbidden', $code, $previous);
    }
}
