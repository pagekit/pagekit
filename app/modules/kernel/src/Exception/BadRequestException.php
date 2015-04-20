<?php

namespace Pagekit\Kernel\Exception;

class BadRequestException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 400, $previous = null)
    {
        parent::__construct($message ?: 'Bad Request', $code, $previous);
    }
}
