<?php

namespace Pagekit\Kernel\Exception;

class ConflictException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 409, $previous = null)
    {
        parent::__construct($message ?: 'Conflict', $code, $previous);
    }
}
