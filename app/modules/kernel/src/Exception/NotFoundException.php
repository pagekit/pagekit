<?php

namespace Pagekit\Kernel\Exception;

class NotFoundException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = null, $code = 404, $previous = null)
    {
        parent::__construct($message ?: 'Not Found', $code, $previous);
    }
}
