<?php

namespace Pagekit\Application\Traits;

use Pagekit\Application\ExceptionListenerWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

trait RouterTrait
{
    /**
     * @see Router::abort()
     */
    public static function abort($code, $message = '', array $headers = [])
    {
        static::router()->abort($code, $message, $headers);
    }

    /**
     * Registers an error handler.
     *
     * @param mixed   $callback
     * @param integer $priority
     */
    public static function error($callback, $priority = -8)
    {
        static::events()->on('kernel.exception', new ExceptionListenerWrapper($callback), $priority);
    }

    /**
     * Handles a Subrequest to call an action internally.
     *
     * @param  string $name
     * @param  array  $parameters
     * @throws \RuntimeException
     * @return Response
     */
    public static function call($name, $parameters = [])
    {
        if (empty(static::request())) {
            throw new \RuntimeException('No Request set.');
        }

        return static::kernel()->handle(
            Request::create(
                $this->generate($name, $parameters), 'GET', [],
                $request->cookies->all(), [],
                $request->server->all()
            ), HttpKernelInterface::SUB_REQUEST);
    }
}
