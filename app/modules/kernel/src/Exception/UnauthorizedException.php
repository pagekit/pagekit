<?php

namespace Pagekit\Kernel\Exception;

class UnauthorizedException extends HttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $previous = null, $code = 401)
    {
        parent::__construct($message ?: 'Unauthorized', $previous, $code);
    }
}
