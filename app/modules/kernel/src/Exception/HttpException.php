<?php

namespace Pagekit\Kernel\Exception;

class HttpException extends \RuntimeException
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param \Exception $previous
     * @param int        $code
     */
    public function __construct($message, $previous = null, $code = 500)
    {
        parent::__construct($message, $code, $previous);
    }
}
