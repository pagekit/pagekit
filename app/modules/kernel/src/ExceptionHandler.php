<?php

namespace Pagekit\Kernel;

use Pagekit\Kernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as DebugExceptionHandler;

class ExceptionHandler extends DebugExceptionHandler
{
    /**
     * {@inheritdoc}
     */
    public function sendPhpResponse($exception)
    {
        if ($exception instanceof HttpException) {
            $exception = FlattenException::create($exception, $exception->getCode());
        }

        parent::sendPhpResponse($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse($exception)
    {
        if ($exception instanceof HttpException) {
            $exception = FlattenException::create($exception, $exception->getCode());
        }

        return parent::createResponse($exception);
    }
}
