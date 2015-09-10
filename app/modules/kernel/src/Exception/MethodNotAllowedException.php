<?php

namespace Pagekit\Kernel\Exception;

class MethodNotAllowedException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $previous = null, $code = 405)
    {
        parent::__construct($message ?: 'Method Not Allowed', $previous, $code);
    }
}
