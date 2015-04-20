<?php

namespace Pagekit\Kernel\Exception;

class MethodNotAllowedException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 405, $previous = null)
    {
        parent::__construct($message ?: 'Method Not Allowed', $code, $previous);
    }
}
