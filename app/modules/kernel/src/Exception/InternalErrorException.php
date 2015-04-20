<?php

namespace Pagekit\Kernel\Exception;

class InternalErrorException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 500, $previous = null)
    {
        parent::__construct($message ?: 'Internal Server Error', $code, $previous);
    }
}
