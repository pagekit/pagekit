<?php

namespace Pagekit\Kernel\Exception;

class InternalErrorException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $previous = null, $code = 500)
    {
        parent::__construct($message ?: 'Internal Server Error', $previous, $code);
    }
}
