<?php

namespace Pagekit\Kernel\Exception;

class ConflictException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $previous = null, $code = 409)
    {
        parent::__construct($message ?: 'Conflict', $previous, $code);
    }
}
